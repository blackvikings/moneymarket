<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Bank;
use App\Models\RtoAgent;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RtoController extends Controller
{
    protected $stages = [
        'New',
        'Checking',
        'Loan Return',
        'Vehicle Detail',
        'Insurance',
        'RTO Done'
    ];

    public function index(Request $request)
    {
        // Show leads that are either in an RTO stage OR have completed the CRM Disbursal stage
        $query = Lead::where(function($q) {
            $q->whereNotNull('rto_stage')
              ->orWhere('crm_stage', 'Closed');
        });

        if ($request->has('stage') && $request->stage != '') {
            $query->where('rto_stage', $request->stage);
        }

        $leads = $query->latest()->paginate(10);
        $stages = $this->stages;

        return view('rto.index', compact('leads', 'stages'));
    }

    public function create()
    {
        return view('rto.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'rto_address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except('rto_address_proof');
        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead'; // Treat as an active lead
        $data['rto_stage'] = 'New';

        if ($request->hasFile('rto_address_proof')) {
            $data['rto_address_proof_path'] = $request->file('rto_address_proof')->store('rto_docs', 'public');
        }

        Lead::create($data);

        return redirect()->route('rto.index')->with('success', 'New RTO Lead added successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['bank', 'rtoAgent', 'rtoCloserDeposits', 'rtoChallans', 'insurance']);
        $stages = $this->stages;
        $currentStageIndex = array_search($lead->rto_stage, $stages);
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
            'rto_hypothecation_status' => $request->has('rto_hypothecation_status'),
            'rto_hypothecation_bank_id' => $request->rto_hypothecation_bank_id,
            'rto_insurance_status' => $request->has('rto_insurance_status'),
            'rto_fitness_status' => $request->has('rto_fitness_status'),
            'rto_tax_status' => $request->has('rto_tax_status'),
            'rto_puc_status' => $request->has('rto_puc_status'),
            'rto_echallan_status' => $request->has('rto_echallan_status'),
            'rto_ncr_status' => $request->has('rto_ncr_status'),
            'rto_challan_status' => $request->has('rto_challan_status'),
            'rto_permit_status' => $request->has('rto_permit_status'),
        ];

        $lead->update($data);

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

        return redirect()->back()->with('success', 'Loan return details updated successfully.');
    }

    public function updateVehicleDetail(Request $request, Lead $lead)
    {
        $request->validate([
            'rto_vehicle_type' => 'required|in:New Vehicle,Old Vehicle',
            'rto_agent_id' => 'nullable|exists:rto_agents,id',
        ]);

        $lead->update([
            'rto_vehicle_type' => $request->rto_vehicle_type,
            'old_financer' => $request->old_financer, // Assuming this field exists from previous migration
            'rto_loan_closed_status' => $request->has('rto_loan_closed_status'),
            'rto_hp_termination_status' => $request->has('rto_hp_termination_status'),
            'rto_agent_id' => $request->rto_agent_id,
            'rto_challan_verified' => $request->has('rto_challan_verified'),
            'rto_verification_status' => $request->has('rto_verification_status'),
            'rto_approval_status' => $request->has('rto_approval_status'),
        ]);

        // Handle Closer Deposits
        if ($request->has('closer_deposits')) {
            $lead->rtoCloserDeposits()->delete(); // Simple way to handle updates: delete all and recreate
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

        return redirect()->back()->with('success', 'Vehicle details updated successfully.');
    }

    public function createInsuranceLead()
    {
        return view('rto.create_insurance_lead');
    }

    public function storeInsuranceLead(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'insurance_address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except('insurance_address_proof');
        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['rto_stage'] = 'Insurance'; // Start directly at the Insurance stage

        if ($request->hasFile('insurance_address_proof')) {
            $data['insurance_address_proof_path'] = $request->file('insurance_address_proof')->store('insurance_docs', 'public');
        }

        $lead = Lead::create($data);

        return redirect()->route('rto.show', $lead)->with('success', 'New Insurance Lead added successfully.');
    }

    public function updateInsuranceDetails(Request $request, Lead $lead)
    {
        $request->validate([
            'insurance_id' => 'required|exists:insurances,id',
            'insurance_start_date' => 'required|date',
            'insurance_end_date' => 'required|date',
            'insurance_type_of_insurance' => 'required|array',
            'insurance_premium_amount' => 'required|numeric',
            'insurance_idv_amount' => 'required|numeric',
        ]);

        $lead->update([
            'insurance_id' => $request->insurance_id,
            'insurance_start_date' => $request->insurance_start_date,
            'insurance_end_date' => $request->insurance_end_date,
            'insurance_type_of_insurance' => implode(', ', $request->insurance_type_of_insurance),
            'insurance_premium_amount' => $request->insurance_premium_amount,
            'insurance_idv_amount' => $request->insurance_idv_amount,
        ]);

        return redirect()->back()->with('success', 'Insurance details updated successfully.');
    }
}
