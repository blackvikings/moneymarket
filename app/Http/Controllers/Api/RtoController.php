<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $user = Auth::user();

        $query = Lead::with(['vehicleName', 'manufacture'])->where(function($q) {
            $q->whereNotNull('rto_stage')
              ->orWhere('crm_stage', 'Closed');
        });

        // Non-admin users can only see their own leads
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->has('stage')) {
            $query->where('rto_stage', $request->stage);
        }

        $leads = $query->latest()->paginate(15);

        return response()->json($leads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'residence_type' => 'required|string',
            'current_address_line' => 'required|string',
            'current_address_pincode' => 'nullable|string|max:20',
            'current_address_state' => 'required|exists:states,id',
            'current_address_city' => 'required|exists:cities,id',
            'permanent_address_line' => 'required|string',
            'permanent_address_pincode' => 'nullable|string|max:20',
            'permanent_address_state' => 'required|exists:states,id',
            'permanent_address_city' => 'required|exists:cities,id',
            'rto_address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $validated;

        // Convert State and City IDs to names
        $data['current_address_state'] = State::find($validated['current_address_state'])->name;
        $data['current_address_city'] = City::find($validated['current_address_city'])->name;
        $data['permanent_address_state'] = State::find($validated['permanent_address_state'])->name;
        $data['permanent_address_city'] = City::find($validated['permanent_address_city'])->name;

        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['rto_stage'] = 'New';
        $data['assigned_to'] = Auth::id();

        if ($request->hasFile('rto_address_proof')) {
            $data['rto_address_proof_path'] = $request->file('rto_address_proof')->store('rto_docs', 'public');
        }

        $lead = Lead::create($data);

        return response()->json(['message' => 'New RTO Lead added successfully.', 'lead' => $lead], 201);
    }

    public function storeInsuranceLead(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'father_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'residence_type' => 'nullable|string',
            'current_address_line' => 'nullable|string',
            'current_address_pincode' => 'nullable|string|max:20',
            'current_address_state' => 'nullable|exists:states,id',
            'current_address_city' => 'nullable|exists:cities,id',
            'permanent_address_line' => 'nullable|string',
            'permanent_address_pincode' => 'nullable|string|max:20',
            'permanent_address_state' => 'nullable|exists:states,id',
            'permanent_address_city' => 'nullable|exists:cities,id',
            'insurance_address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $validated;

        // Convert State and City IDs to names if they exist
        if(!empty($validated['current_address_state'])) {
            $data['current_address_state'] = State::find($validated['current_address_state'])->name;
        }
        if(!empty($validated['current_address_city'])) {
            $data['current_address_city'] = City::find($validated['current_address_city'])->name;
        }
        if(!empty($validated['permanent_address_state'])) {
            $data['permanent_address_state'] = State::find($validated['permanent_address_state'])->name;
        }
        if(!empty($validated['permanent_address_city'])) {
            $data['permanent_address_city'] = City::find($validated['permanent_address_city'])->name;
        }

        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['rto_stage'] = 'RTO Done';
        $data['insurance_lead_stage'] = 'New';
        $data['assigned_to'] = Auth::id();

        if ($request->hasFile('insurance_address_proof')) {
            $data['insurance_address_proof_path'] = $request->file('insurance_address_proof')->store('insurance_docs', 'public');
        }

        $lead = Lead::create($data);

        return response()->json(['message' => 'New Insurance Lead added successfully.', 'lead' => $lead], 201);
    }

    public function show(Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->load(['bank', 'rtoAgent', 'rtoCloserDeposits', 'rtoChallans', 'insurance', 'vehicleName', 'manufacture']);
        return response()->json($lead);
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|in:Yes,No',
            'current_stage' => 'required|in:' . implode(',', $this->stages),
        ]);

        $currentStageIndex = array_search($request->current_stage, $this->stages);

        if ($request->action === 'Yes') {
            if ($currentStageIndex < count($this->stages) - 1) {
                $nextStage = $this->stages[$currentStageIndex + 1];
                $lead->update(['rto_stage' => $nextStage]);
                return response()->json(['message' => 'Stage updated to ' . $nextStage, 'lead' => $lead]);
            }
        }

        return response()->json(['message' => 'No action taken.']);
    }

    public function updateVehicleDetail(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

        // Handle Closer Deposits and Challans if needed (omitted for brevity as they are complex arrays)

        if ($request->has('save_and_next') && $request->save_and_next == 1) {
            if ($lead->rto_stage === 'Vehicle Detail') {
                $lead->update(['rto_stage' => 'RTO Done']);
                return response()->json(['message' => 'Vehicle details updated. Lead moved to RTO Done.', 'lead' => $lead]);
            }

            $currentStageIndex = array_search($lead->rto_stage, $this->stages);
            if ($currentStageIndex !== false && $currentStageIndex < count($this->stages) - 1) {
                $nextStage = $this->stages[$currentStageIndex + 1];
                $lead->update(['rto_stage' => $nextStage]);
                return response()->json(['message' => 'Vehicle details updated and moved to ' . $nextStage, 'lead' => $lead]);
            }
        }

        return response()->json(['message' => 'Vehicle details updated successfully.', 'lead' => $lead]);
    }

    public function updateRtoDone(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rto_approved_doc' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('rto_approved_doc')) {
            if ($lead->rto_approved_doc_path) {
                Storage::disk('public')->delete($lead->rto_approved_doc_path);
            }
            $path = $request->file('rto_approved_doc')->store('rto_approved_docs', 'public');

            $lead->update([
                'rto_approved_doc_path' => $path,
                'insurance_lead_stage' => 'New' // Move to Insurance Module
            ]);
        }

        return response()->json(['message' => 'RTO Document uploaded. Lead moved to Insurance Module.', 'lead' => $lead]);
    }

    public function rejectLead(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $lead->update([
            'rto_stage' => 'Loan Return',
            'loan_return_reason' => $request->rejection_reason,
            'loan_return_date' => now(),
        ]);

        return response()->json(['message' => 'Lead rejected and moved to Loan Return.', 'lead' => $lead]);
    }
}
