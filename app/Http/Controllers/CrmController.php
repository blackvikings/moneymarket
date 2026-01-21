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
        'Disbursal'
        // 'Closed' removed as per request
    ];

    public function __construct()
    {
        // Add permissions for CRM module later if needed
    }

    public function index(Request $request)
    {
        // Show leads that are relevant to CRM:
        // 1. Explicitly 'Loan' type leads that are active (not Rejected/Not_Active)
        // 2. Leads with 'Bank Assign' status (legacy or flow specific)
        // 3. Leads with 'Lead' status and a bank assigned (legacy)

        $query = Lead::with(['bank', 'bankBranch'])
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->where('lead_type', 'Loan')
                        ->whereNotIn('status', ['Rejected', 'Not_Active']);
                })
                ->orWhere('status', 'Bank Assign')
                ->orWhere(function($sub) {
                    $sub->where('status', 'Lead')->whereNotNull('bank_id');
                });
            });

        if ($request->has('stage') && $request->stage != '') {
            $query->where('crm_stage', $request->stage);
        }

        $leads = $query->latest()->paginate(10);
        $stages = $this->stages;

        // Calculate counts for each stage using the same base logic
        $baseCountQuery = Lead::where(function($q) {
            $q->where(function($sub) {
                $sub->where('lead_type', 'Loan')
                    ->whereNotIn('status', ['Rejected', 'Not_Active']);
            })
            ->orWhere('status', 'Bank Assign')
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
        ]);

        $lead->update([
            'disbursal_date' => $request->disbursal_date,
            'finance_amount' => $request->finance_amount,
            'disbursal_amount' => $request->disbursal_amount,
            'crm_disbursal_at' => now(),
            'crm_stage' => 'Disbursal', // Move to Disbursal stage
            // Automatically move to RTO
            'rto_stage' => 'New',
            'rto_new_at' => now(),
        ]);

        return redirect()->route(get_route_prefix() . 'crm.index', $lead)->with('success', 'Disbursal details saved. Lead moved to RTO.');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return (new CrmExport($request))->download('crm-leads.csv');
    }
}
