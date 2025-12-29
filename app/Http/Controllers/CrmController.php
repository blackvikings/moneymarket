<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

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
        $query = Lead::with(['bank', 'bankBranch'])->where('status', 'Lead'); // Eager load relationships

        if ($request->has('stage') && $request->stage != '') {
            $query->where('crm_stage', $request->stage);
        }

        $leads = $query->latest()->paginate(10);
        $stages = $this->stages;

        // Calculate counts for each stage
        $counts = [];
        $counts['All'] = Lead::where('status', 'Lead')->count();
        foreach ($stages as $stage) {
            $counts[$stage] = Lead::where('status', 'Lead')->where('crm_stage', $stage)->count();
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
        ]);

        $currentStageIndex = array_search($request->current_stage, $this->stages);

        if ($request->action === 'Yes') {
            // Move to the next stage
            if ($currentStageIndex < count($this->stages) - 1) {
                $nextStage = $this->stages[$currentStageIndex + 1];
                $lead->crm_stage = $nextStage;

                // Set timestamp for the completed stage
                $timestampField = 'crm_' . strtolower(str_replace(' ', '_', $request->current_stage)) . '_at';
                if (Schema::hasColumn('leads', $timestampField)) {
                    $lead->$timestampField = now();
                }

                $lead->save();
                return redirect()->back()->with('success', 'Stage updated to ' . $nextStage);
            }
        }

        // If 'No' or it's the last stage, do nothing but maybe log it
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
            'crm_stage' => 'Closed', // Move to final stage
        ]);

        return redirect()->route('crm.show', $lead)->with('success', 'Disbursal details saved successfully.');
    }
}
