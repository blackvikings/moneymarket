<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Exports\CrmExport;

class CrmController extends Controller
{
    protected $stages = [
        'New',
        'Login',
        'FI Document',
        'Vehicle Evaluation',
        'Credit',
        'Approved',
        'Agreement',
        'Disbursal',
        'Closed' // Final stage after disbursal
    ];

    public function __construct()
    {
        // Add permissions for CRM module later if needed
    }

    public function index(Request $request)
    {
        // Show leads that are in 'Bank Assign' status OR 'Lead' status with a bank assigned
        // The previous logic was: where('status', 'Lead')->where('bank_id', '!=', null)
        // But the LeadController now sets status to 'Bank Assign' for Loan leads.
        // So we need to include 'Bank Assign' status as well.

        $query = Lead::with(['bank', 'bankBranch'])
            ->where(function($q) {
                $q->where('status', 'Bank Assign')
                  ->orWhere(function($sub) {
                      $sub->where('status', 'Lead')->whereNotNull('bank_id');
                  });
            });

        if ($request->has('stage') && $request->stage != '') {
            $query->where('crm_stage', $request->stage);
        }

        $leads = $query->latest()->paginate(10);
        $stages = $this->stages;

        // Calculate counts for each stage
        $baseCountQuery = Lead::where(function($q) {
            $q->where('status', 'Bank Assign')
              ->orWhere(function($sub) {
                  $sub->where('status', 'Lead')->whereNotNull('bank_id');
              });
        });

        $counts = [];
        $counts['All'] = $baseCountQuery->count();
        foreach ($stages as $stage) {
            $counts[$stage] = (clone $baseCountQuery)->where('crm_stage', $stage)->count();
        }

        return view('crm.index', compact('leads', 'stages', 'counts'));
    }

    public function show(Lead $lead)
    {
        $lead->load(['bank', 'bankBranch', 'firm']);
        $stages = $this->stages;
        $currentStageIndex = array_search($lead->crm_stage, $stages);

        return view('crm.show', compact('lead', 'stages', 'currentStageIndex'));
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $request->validate([
            'action' => 'required|in:Yes,No',
            'current_stage' => 'required|in:' . implode(',', $this->stages),
            'completed_stages' => 'nullable|array',
        ]);

        $currentStageIndex = array_search($request->current_stage, $this->stages);

        if ($request->action === 'Yes' && $request->has('completed_stages')) {
            $completedStages = $request->completed_stages;
            $lastCompletedStage = null;
            $lastCompletedIndex = -1;

            // Find the furthest stage selected
            foreach ($completedStages as $stage) {
                $index = array_search($stage, $this->stages);
                if ($index !== false && $index > $lastCompletedIndex) {
                    $lastCompletedIndex = $index;
                    $lastCompletedStage = $stage;
                }
            }

            // If a valid future stage is selected
            if ($lastCompletedIndex > $currentStageIndex) {
                // Update timestamps for all intermediate stages up to the selected one
                for ($i = $currentStageIndex + 1; $i <= $lastCompletedIndex; $i++) {
                    $stageName = $this->stages[$i];
                    $timestampField = 'crm_' . strtolower(str_replace(' ', '_', $stageName)) . '_at';
                    if (Schema::hasColumn('leads', $timestampField)) {
                        $lead->$timestampField = now();
                    }
                }

                // Move to the NEXT stage after the last completed one
                if (isset($this->stages[$lastCompletedIndex + 1])) {
                    $nextStage = $this->stages[$lastCompletedIndex + 1];
                    $lead->crm_stage = $nextStage;
                    $lead->save();
                    return redirect()->back()->with('success', 'Stages updated. Lead moved to ' . $nextStage);
                } else {
                    // Special case for 'Closed': If 'Closed' is checked, stay in 'Closed'.
                    if ($this->stages[$lastCompletedIndex] === 'Closed') {
                        $lead->crm_stage = 'Closed';
                        $lead->save();
                        return redirect()->back()->with('success', 'Lead Closed successfully.');
                    }

                    // Otherwise, move to next
                     $nextStage = $this->stages[$lastCompletedIndex + 1]; // This might still be out of bounds if not careful
                     // But we handled Closed above.

                     // Re-check bounds just in case
                     if (isset($this->stages[$lastCompletedIndex + 1])) {
                        $lead->crm_stage = $nextStage;
                        $lead->save();
                        return redirect()->back()->with('success', 'Stages updated. Lead moved to ' . $nextStage);
                     }
                }
            }
        }

        return redirect()->back()->with('info', 'No action taken.');
    }

    public function storeDisbursal(Request $request, Lead $lead)
    {
        $request->validate([
            'disbursal_date' => 'required|date',
            'finance_amount' => 'required|numeric',
            'disbursal_amount' => 'required|numeric',
            'amt_rec_firm' => 'required|numeric',
            'amt_rec_lead' => 'required|numeric',
            'amt_rec_old_financer' => 'required|numeric',
        ]);

        $totalReceived = $request->amt_rec_firm + $request->amt_rec_lead + $request->amt_rec_old_financer;
        if ($totalReceived != $request->disbursal_amount) {
            return redirect()->back()->withErrors(['disbursal_amount' => 'Total received amount must equal the disbursal amount.']);
        }

        $lead->update([
            'disbursal_date' => $request->disbursal_date,
            'finance_amount' => $request->finance_amount,
            'disbursal_amount' => $request->disbursal_amount,
            'amt_rec_firm' => $request->amt_rec_firm,
            'amt_rec_lead' => $request->amt_rec_lead,
            'amt_rec_old_financer' => $request->amt_rec_old_financer,
            'crm_disbursal_at' => now(),
            'crm_stage' => 'Disbursal', // Move to Disbursal stage first
        ]);

        return redirect()->route(get_route_prefix() . 'crm.show', $lead)->with('success', 'Disbursal details saved successfully.');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return (new CrmExport($request))->download('crm-leads.csv');
    }
}
