<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Bank;
use App\Models\RtoAgent;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\RtoExport;

class RtoController extends Controller
{
    protected $stages = [
        'New',
        'Checking',
        'Vehicle Detail',
        'RTO Done',
        'Loan Return'
    ];

    public function index(Request $request)
    {
        // Show leads that are explicitly RTO type OR have completed the CRM Disbursal stage (Closed)
        // We removed whereNotNull('rto_stage') because it defaults to 'New', causing all leads to show up.
        $query = Lead::with(['vehicleName', 'manufacture'])->where(function($q) {
            $q->where('lead_type', 'RTO')
              ->orWhere('crm_stage', 'Closed');
        });

        if ($request->has('stage') && $request->stage != '') {
            if ($request->stage == 'New') {
                // For 'New' stage, include explicit 'New' OR null rto_stage
                $query->where(function($q) {
                    $q->where('rto_stage', 'New')
                      ->orWhereNull('rto_stage');
                });
            } else {
                $query->where('rto_stage', $request->stage);
            }
        }

        $leads = $query->latest()->paginate(10);
        $stages = $this->stages;

        // Calculate counts for each stage
        // Base query for counts must match the main query's base conditions
        $baseCountQuery = Lead::where(function($q) {
            $q->where('lead_type', 'RTO')
              ->orWhere('crm_stage', 'Closed');
        });

        $counts = [];
        $counts['All'] = $baseCountQuery->count();

        foreach ($stages as $stage) {
            if ($stage == 'New') {
                $counts[$stage] = (clone $baseCountQuery)->where(function($q) {
                    $q->where('rto_stage', 'New')
                      ->orWhereNull('rto_stage');
                })->count();
            } else {
                $counts[$stage] = (clone $baseCountQuery)->where('rto_stage', $stage)->count();
            }
        }

        return view('rto.index', compact('leads', 'stages', 'counts'));
    }

    public function create()
    {
        return view('rto.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'residence_type' => 'required|string',
            'current_address_line' => 'required|string',
            'permanent_address_line' => 'required|string',
            'rto_address_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except('rto_address_proof');
        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead'; // Treat as an active lead
        $data['rto_stage'] = 'New';
        $data['lead_type'] = 'RTO'; // Ensure lead type is set
        $data['rto_new_at'] = now(); // Start aging

        if ($request->hasFile('rto_address_proof')) {
            $data['rto_address_proof_path'] = $request->file('rto_address_proof')->store('rto_docs', 'public');
        }

        Lead::create($data);

        return redirect()->route(get_route_prefix() . 'rto.index')->with('success', 'New RTO Lead added successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['bank', 'rtoAgent', 'rtoCloserDeposits', 'rtoChallans', 'insurance', 'vehicleName', 'manufacture']);
        $stages = $this->stages;
        // Handle null rto_stage as 'New'
        $currentStage = $lead->rto_stage ?? 'New';
        $currentStageIndex = array_search($currentStage, $stages);
        $banks = Bank::where('is_active', true)->get();
        $rtoAgents = RtoAgent::where('is_active', true)->get();
        $insurances = Insurance::where('is_active', true)->get();

        return view('rto.show', compact('lead', 'stages', 'currentStageIndex', 'banks', 'rtoAgents', 'insurances'));
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $request->validate([
            'action' => 'required|in:Yes,No',
            'current_stage' => 'required|in:' . implode(',', $this->stages),
        ]);

        // Handle null rto_stage as 'New'
        $currentStage = $lead->rto_stage ?? 'New';
        $currentStageIndex = array_search($request->current_stage, $this->stages);

        if ($request->action === 'Yes') {
            if ($currentStageIndex < count($this->stages) - 1) {
                $nextStage = $this->stages[$currentStageIndex + 1];
                $lead->update(['rto_stage' => $nextStage]);
                return redirect()->back()->with('success', 'Stage updated to ' . $nextStage);
            }
        }

        return redirect()->back()->with('info', 'No action taken.');
    }

    public function updateChecklist(Request $request, Lead $lead)
    {
        // Handle checklist updates for the "Checking" stage
        $data = [
            'rto_hsrp_status' => $request->has('rto_hsrp_status'),
            'rto_hsrp_remark' => $request->rto_hsrp_remark,
            'rto_hypothecation_status' => $request->has('rto_hypothecation_status'),
            'rto_hypothecation_remark' => $request->rto_hypothecation_remark,
            'rto_hypothecation_bank_id' => $request->rto_hypothecation_bank_id,
            'rto_insurance_status' => $request->has('rto_insurance_status'),
            'rto_insurance_remark' => $request->rto_insurance_remark,
            'rto_fitness_status' => $request->has('rto_fitness_status'),
            'rto_fitness_remark' => $request->rto_fitness_remark,
            'rto_tax_status' => $request->has('rto_tax_status'),
            'rto_tax_remark' => $request->rto_tax_remark,
            'rto_puc_status' => $request->has('rto_puc_status'),
            'rto_puc_remark' => $request->rto_puc_remark,
            'rto_echallan_status' => $request->has('rto_echallan_status'),
            'rto_echallan_remark' => $request->rto_echallan_remark,
            'rto_ncr_status' => $request->has('rto_ncr_status'),
            'rto_ncr_remark' => $request->rto_ncr_remark,
            'rto_challan_status' => $request->has('rto_challan_status'),
            'rto_challan_remark' => $request->rto_challan_remark,
            'rto_permit_status' => $request->has('rto_permit_status'),
            'rto_permit_remark' => $request->rto_permit_remark,
        ];

        $lead->update($data);

        if ($request->has('save_and_next')) {
            $currentStageIndex = array_search($lead->rto_stage, $this->stages);

            // Special case: If stage is 'New', jump to 'Vehicle Detail' (skipping 'Checking' state as we just completed it)
            if ($lead->rto_stage === 'New') {
                $lead->update(['rto_stage' => 'Vehicle Detail']);
                return redirect()->back()->with('success', 'Checklist updated and moved to Vehicle Detail');
            }

            if ($currentStageIndex !== false && $currentStageIndex < count($this->stages) - 1) {
                $nextStage = $this->stages[$currentStageIndex + 1];
                $lead->update(['rto_stage' => $nextStage]);
                return redirect()->back()->with('success', 'Checklist updated and moved to ' . $nextStage);
            }
        }

        return redirect()->back()->with('success', 'Checklist updated successfully.');
    }

    public function updateLoanReturn(Request $request, Lead $lead)
    {
        $request->validate([
            'loan_return_date' => 'required|date',
            'loan_return_reason' => 'required|string',
        ]);

        $lead->update([
            'loan_return_date' => $request->loan_return_date,
            'loan_return_reason' => $request->loan_return_reason,
        ]);

        // Loan Return is now the final stage for rejected leads, so no "Save & Next" logic needed to move forward.
        // It stays in Loan Return.

        return redirect()->back()->with('success', 'Loan return details updated successfully.');
    }

    public function updateVehicleDetail(Request $request, Lead $lead)
    {
        $request->validate([
            'rto_vehicle_type' => 'required|in:New Vehicle,Old Vehicle',
            'rto_agent_id' => 'nullable|exists:rto_agents,id',
            'rto_approved_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = [
            'rto_vehicle_type' => $request->rto_vehicle_type,
            'old_financer' => $request->old_financer,
            'rto_loan_closed_status' => $request->has('rto_loan_closed_status'),
            'rto_hp_termination_status' => $request->has('rto_hp_termination_status'),
            'rto_agent_id' => $request->rto_agent_id,
            'rto_challan_verified' => $request->has('rto_challan_verified'),
            'rto_verification_status' => $request->has('rto_verification_status'),
            'rto_approval_status' => $request->has('rto_approval_status'),
        ];

        if ($request->hasFile('rto_approved_doc')) {
            if ($lead->rto_approved_doc_path) {
                Storage::disk('public')->delete($lead->rto_approved_doc_path);
            }
            $data['rto_approved_doc_path'] = $request->file('rto_approved_doc')->store('rto_approved_docs', 'public');
        }

        $lead->update($data);

        // Handle Closer Deposits
        if ($request->has('closer_deposits')) {
            $lead->rtoCloserDeposits()->delete();
            foreach ($request->closer_deposits as $deposit) {
                if ($deposit['amount'] && $deposit['date']) {
                    $lead->rtoCloserDeposits()->create($deposit);
                }
            }
        }

        // Handle Challans
        if ($request->has('challans')) {
            $lead->rtoChallans()->delete();
            foreach ($request->challans as $challan) {
                if ($challan['type'] && $challan['date'] && $challan['amount']) {
                    $lead->rtoChallans()->create([
                        'challan_type' => $challan['type'],
                        'date' => $challan['date'],
                        'amount' => $challan['amount'],
                    ]);
                }
            }
        }

        if ($request->has('save_and_next')) {
            // Reverted: Just move to RTO Done, do NOT initialize Insurance yet
            if ($lead->rto_stage === 'Vehicle Detail') {
                $lead->update(['rto_stage' => 'RTO Done']);
                return redirect()->back()->with('success', 'Vehicle details updated. Lead moved to RTO Done.');
            }
        }

        return redirect()->back()->with('success', 'Vehicle details updated successfully.');
    }

    public function updateRtoDone(Request $request, Lead $lead)
    {
        $request->validate([
            'rto_approved_doc' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'remark' => 'nullable|string|max:500',
        ]);

        $data = [
            'insurance_lead_stage' => 'New', // Move to Insurance Module
            'remark' => $request->remark,
            'rto_done_at' => now(), // Mark RTO as done
        ];

        if ($request->hasFile('rto_approved_doc')) {
            if ($lead->rto_approved_doc_path) {
                Storage::disk('public')->delete($lead->rto_approved_doc_path);
            }
            $data['rto_approved_doc_path'] = $request->file('rto_approved_doc')->store('rto_approved_docs', 'public');
        }

        $lead->update($data);

        return redirect()->back()->with('success', 'RTO Document uploaded. Lead moved to Insurance Module.');
    }

    public function rejectLead(Request $request, Lead $lead)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $lead->update([
            'rto_stage' => 'Loan Return',
            'loan_return_reason' => $request->rejection_reason,
            'loan_return_date' => now(),
        ]);

        return redirect()->back()->with('warning', 'Lead rejected and moved to Loan Return.');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return (new RtoExport($request))->download('rto-leads.csv');
    }
}
