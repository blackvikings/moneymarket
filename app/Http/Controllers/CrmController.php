<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ReferralLog;
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
        'Payment Release Detail'
    ];

    public function __construct()
    {
        $this->middleware('permission:view crm', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit crm', ['only' => ['updateStage', 'storeDisbursal']]);
        $this->middleware('permission:export crm', ['only' => ['export']]);
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

        $leads = $query->latest()->paginate(10)->appends($request->query());
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
        $lead->load(['bank', 'bankBranch', 'firm', 'manufacture', 'vehicleName', 'statusLogs.user', 'currentCity', 'currentState', 'permanentCity', 'permanentState']);
        $stages = $this->stages;
        $currentStageIndex = array_search($lead->crm_stage, $stages);

        return view('crm.show-modal', compact('lead', 'stages', 'currentStageIndex'))->render();
    }

    public function updateStage(Request $request, Lead $lead)
    {
        // Validate only completed_stages, as action and current_stage are no longer sent directly
        $request->validate([
            'completed_stages' => 'nullable|array', // Allow array of stages, can be empty
            'completed_stages.*' => 'in:' . implode(',', $this->stages), // Each stage must be valid
            'disbursal_date' => 'nullable|date',
            'finance_amount' => 'nullable|numeric',
            'disbursal_amount' => 'nullable|numeric',
            'move_to_rto' => 'nullable|string',
            'move_to_insurance' => 'nullable|string',
            'insurance_expiry_date' => 'nullable|required_if:move_to_insurance,yes|date',
            'payment_amount' => 'nullable|numeric',
            'payment_date' => 'nullable|date',
            'payment_remarks' => 'nullable|string',
        ]);

        $currentLeadStageIndex = array_search($lead->crm_stage, $this->stages);
        $newStage = $lead->crm_stage; // Initialize with current stage
        // If completed_stages are provided, find the highest selected stage that is a valid progression
        if ($request->has('completed_stages') && is_array($request->completed_stages)) {
            $selectedStages = $request->completed_stages;
            $highestSelectedStageIndex = $currentLeadStageIndex;
            $potentialNewStage = $lead->crm_stage;

            foreach ($selectedStages as $stage) {
                $stageIndex = array_search($stage, $this->stages);
                // Ensure the selected stage is valid and is a progression from the current stage
                if ($stageIndex !== false && $stageIndex > $highestSelectedStageIndex) {
                    $highestSelectedStageIndex = $stageIndex;
                    $potentialNewStage = $stage;
                }
            }

            // Check if the potential new stage is actually different and a valid progression
            if ($potentialNewStage !== $lead->crm_stage) {
                // Update timestamps for all intermediate stages up to the selected one
                $timestampFieldAll = '';
                for ($i = $currentLeadStageIndex + 1; $i <= $highestSelectedStageIndex; $i++) {
                    $stageName = $this->stages[$i];
                    $timestampField = 'crm_' . strtolower(str_replace(' ', '_', $stageName)) . '_at';
                    if (Schema::hasColumn('leads', $timestampField)) {
                        // Only update if it's not already set (or if you want to re-stamp on every update)
                        if (empty($lead->$timestampField)) {
                            $lead->$timestampField = now();
                        }
                    }
                    $timestampFieldAll = $timestampFieldAll . $timestampField . ',';
                }

                $lead->crm_stage = $potentialNewStage;

                // Handle disbursal specific fields if the new stage is Disbursal
                if ($potentialNewStage === 'Disbursal') {
                    // Enforce required disbursal fields when moving to Disbursal via update-stage (e.g. from Approved).
                    $request->validate([
                        'disbursal_date' => 'required|date',
                        'finance_amount' => 'required|numeric',
                        'disbursal_amount' => 'required|numeric',
                    ]);

                    $lead->disbursal_date = $request->disbursal_date;
                    $lead->finance_amount = $request->finance_amount;
                    $lead->disbursal_amount = $request->disbursal_amount;
                    $lead->crm_disbursal_at = now(); // Ensure disbursal timestamp is set

                    // Handle move to RTO

                    if ($request->move_to_rto === 'yes' ) {
                        $lead->rto_stage = 'New';
                        $lead->rto_new_at = now();
                    }
                    // Handle move to Insurance
                    if ($request->move_to_insurance == 'yes') {
                        $lead->insurance_lead_stage = 'New';
                        $lead->insurance_end_date = $request->insurance_expiry_date;
                    }
                }

                if ($potentialNewStage === 'Payment Release Detail') {
                     $request->validate([
                        'payment_amount' => 'required|numeric',
                        'payment_date' => 'required|date',
                        'payment_remarks' => 'nullable|string',
                    ]);
                    $lead->payment_amount = $request->payment_amount;
                    $lead->payment_date = $request->payment_date;
                    $lead->payment_remarks = $request->payment_remarks;
                    $lead->payment_release_detail_at = now();
                }


                $lead->save();

                // If it's an AJAX request, return JSON
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Stage updated to ' . $potentialNewStage, 'lead' => $lead]);
                }
                // Otherwise, redirect with a success message
                return redirect()->back()->with('success', 'Stage updated to ' . $potentialNewStage);
            }
        }

        // If no stage change detected or no completed_stages provided
        if ($request->expectsJson()) {
            return response()->json(['message' => 'No action taken or no stage change detected.']);
        }
        return redirect()->back()->with('info', 'No action taken or no stage change detected.');
    }

    public function storeDisbursal(Request $request, Lead $lead)
    {
        $request->validate([
            'disbursal_date' => 'required|date',
            'finance_amount' => 'required|numeric',
            'disbursal_amount' => 'required|numeric',
            'move_to_rto' => 'nullable|boolean',
            'move_to_insurance' => 'nullable|boolean',
            'insurance_expiry_date' => 'nullable|required_if:move_to_insurance,1|date',
        ]);

        $data = [
            'disbursal_date' => $request->disbursal_date,
            'finance_amount' => $request->finance_amount,
            'disbursal_amount' => $request->disbursal_amount,
            'crm_disbursal_at' => now(),
            'crm_stage' => 'Disbursal', // Move to Disbursal stage
        ];

        $messages = ['Disbursal details saved.'];
        // Move to RTO if requested
        if ($request->boolean('move_to_rto') === true) {
            $data['rto_stage'] = 'New';
            $data['rto_new_at'] = now();
            $messages[] = 'Lead moved to RTO.';
        }

        // Move to Insurance if requested
        if ($request->boolean('move_to_insurance') === true) {
            $data['insurance_lead_stage'] = 'New';
            $data['insurance_end_date'] = $request->insurance_expiry_date;
            $messages[] = 'Lead moved to Insurance.';
        }

        $lead->update($data);

        // Create Referral Log if applicable
        if ($lead->referral_amount > 0) {
            ReferralLog::create([
                'lead_id' => $lead->id,
                'amount' => $lead->referral_amount,
                'status' => 'Pending',
            ]);
            $messages[] = 'Referral payment logged.';
        }


        return redirect()->route(get_route_prefix() . 'crm.index', $lead)->with('success', implode(' ', $messages));
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !auth()->user()->can('export crm')) {
            abort(403, 'Unauthorized action.');
        }
        return (new CrmExport($request))->download('crm-leads.csv');
    }
}
