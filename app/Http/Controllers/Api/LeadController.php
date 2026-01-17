<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lead::with(['assignedTo', 'bank']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Non-admin users can only see leads assigned to them
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        $leads = $query->latest()->paginate(15);

        return response()->json($leads);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'firm_id' => 'required|exists:firms,id',
            'contact_number' => 'required|string|max:20',
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
            'lead_type' => 'required|in:Loan,RTO,Insurance',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $data = $validated;
        $user = Auth::user();
        // Convert State and City IDs to names
        $data['current_address_state'] = State::find($validated['current_address_state'])->name;
        $data['current_address_city'] = City::find($validated['current_address_city'])->name;
        $data['permanent_address_state'] = State::find($validated['permanent_address_state'])->name;
        $data['permanent_address_city'] = City::find($validated['permanent_address_city'])->name;

        $data['created_by'] = $user->id;
        $data['status'] = 'New';

        // Force assignment for non-admins, or use provided value for admins
        if ($user->hasRole('admin') || $user->hasRole('Team Lead')) {
            $data['assigned_to'] = $request->input('assigned_to', $user->id);
        } else {
            $data['assigned_to'] = $user->id;
        }

        $lead = Lead::create($data);

        // Log initial status
        $lead->statusLogs()->create([
            'status' => 'New',
            'remark' => 'Lead created via API.',
            'created_by' => $user->id,
        ]);

        return response()->json(['message' => 'Lead created successfully.', 'lead' => $lead], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        $user = Auth::user();

        // Ensure non-admin users can only see their own leads
        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->load(['assignedTo', 'createdBy', 'bank', 'bankBranch', 'firm', 'manufacture', 'vehicleName', 'insurance', 'statusLogs.user']);
        return response()->json($lead);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            // Basic Details
            'customer_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'firm_id' => 'required|exists:firms,id',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'residence_type' => 'required|string',
            'lead_type' => 'required|in:Loan,RTO,Insurance',
            // Address
            'current_address_line' => 'required|string',
            'current_address_pincode' => 'nullable|string|max:20',
            'current_address_state' => 'required|exists:states,id',
            'current_address_city' => 'required|exists:cities,id',
            'permanent_address_line' => 'required|string',
            'permanent_address_pincode' => 'nullable|string|max:20',
            'permanent_address_state' => 'required|exists:states,id',
            'permanent_address_city' => 'required|exists:cities,id',
            // Job Profile
            'job_profile_type' => 'required|string',
            'nature_of_work' => 'nullable|string',
            'salaried_type' => 'nullable|string',
            'other_company_name' => 'nullable|string',
            'designation' => 'nullable|string',
            'income_proof_available' => 'required|boolean',
            'itr_amount' => 'nullable|numeric',
            'itr_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'approx_income' => 'nullable|numeric',
            'business_proof_available' => 'nullable|boolean',
            'business_proof_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // Loan Details
            'loan_type' => 'nullable|string',
            'other_loan_name' => 'nullable|string',
            'vehicle_loan_type' => 'nullable|string',
            'other_vehicle_loan_name' => 'nullable|string',
            'vehicle_purchase_type' => 'nullable|string',
            'manufacture_id' => 'nullable|exists:vehicle_manufactures,id',
            'vehicle_variant' => 'nullable|string',
            'showroom_price' => 'nullable|numeric',
            'on_road_price' => 'nullable|numeric',
            'rc_number' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'chassis_number' => 'nullable|string',
            'mfg_year' => 'nullable|string',
            'mfg_name' => 'nullable|string',
            'model_name' => 'nullable|string',
            'variant' => 'nullable|string',
            'owner_serial_number' => 'nullable|string',
            'insurance_available' => 'nullable|boolean',
            'insurance_id' => 'nullable|exists:insurances,id',
            'insurance_start_date' => 'nullable|date',
            'insurance_end_date' => 'nullable|date',
            'insurance_amount' => 'nullable|numeric',
            'hypothecation_status' => 'nullable|boolean',
            'old_financer' => 'nullable|string',
            'close_amount' => 'nullable|numeric',
            'emi_paid_count' => 'nullable|integer',
            // References
            'ref1_name' => 'nullable|string',
            'ref1_mobile' => 'nullable|string',
            'ref1_email' => 'nullable|email',
            'ref1_address' => 'nullable|string',
            'ref2_name' => 'nullable|string',
            'ref2_mobile' => 'nullable|string',
            'ref2_email' => 'nullable|email',
            'ref2_address' => 'nullable|string',
            // Amount
            'old_loan_close_amount' => 'required|numeric',
            'required_in_hand_amount' => 'nullable|numeric',
            // Co-applicant & Guarantor
            'has_co_applicant' => 'nullable|boolean',
            'co_name' => 'nullable|string',
            'co_relation' => 'nullable|string',
            'has_guarantor' => 'nullable|boolean',
            'guarantor_name' => 'nullable|string',
            'guarantor_relation' => 'nullable|string',
            // Assignment
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $data = $validated;

        // Convert State and City IDs to names
        $data['current_address_state'] = State::find($validated['current_address_state'])->name;
        $data['current_address_city'] = City::find($validated['current_address_city'])->name;
        $data['permanent_address_state'] = State::find($validated['permanent_address_state'])->name;
        $data['permanent_address_city'] = City::find($validated['permanent_address_city'])->name;

        // Handle file uploads
        if ($request->hasFile('itr_document')) {
            if ($lead->itr_document_path) {
                Storage::disk('public')->delete($lead->itr_document_path);
            }
            $data['itr_document_path'] = $request->file('itr_document')->store('itr_documents', 'public');
        }
        if ($request->hasFile('business_proof_document')) {
            if ($lead->business_proof_document_path) {
                Storage::disk('public')->delete($lead->business_proof_document_path);
            }
            $data['business_proof_document_path'] = $request->file('business_proof_document')->store('business_proofs', 'public');
        }

        $lead->update($data);

        return response()->json(['message' => 'Lead updated successfully.', 'lead' => $lead->fresh()]);
    }


    /**
     * Update the status of the specified resource in storage.
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $user = Auth::user();

        // Ensure non-admin users can only update their own leads
        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'action' => 'required|in:interested,not_interested,follow_up,qualify,reject,proceed_to_bank_assign',
            'remark' => 'required_if:action,not_interested|required_if:action,follow_up|required_if:action,reject|string|nullable',
            'follow_up_date' => 'required_if:action,follow_up|date|nullable',
            'follow_up_time' => 'required_if:action,follow_up|nullable',
            'bank_id' => 'required_if:action,qualify|exists:banks,id|nullable',
            'bank_branch_id' => 'required_if:action,qualify|exists:bank_branches,id|nullable',
        ]);

        $action = $validated['action'];
        $data = [];
        $logData = ['created_by' => $user->id];

        switch ($action) {
            case 'interested':
                $data['status'] = 'Prospect';
                $logData['status'] = 'Prospect';
                $logData['remark'] = 'Marked as Interested.';
                break;
            case 'not_interested':
                $data['status'] = 'Not_Active';
                $data['remark'] = $validated['remark'];
                $logData['status'] = 'Not_Active';
                $logData['remark'] = $validated['remark'];
                break;
            case 'follow_up':
                $data = [
                    'status' => 'Prospect',
                    'follow_up_date' => $validated['follow_up_date'],
                    'follow_up_time' => $validated['follow_up_time'],
                    'remark' => $validated['remark'],
                ];
                $logData['status'] = 'Prospect';
                $logData['remark'] = 'Follow-up set: ' . $validated['remark'];
                break;
            case 'qualify':
                $data = [
                    'status' => 'Lead',
                    'bank_id' => $validated['bank_id'],
                    'bank_branch_id' => $validated['bank_branch_id'],
                    'converted_at' => now(),
                    'crm_stage' => 'New', // Initialize CRM stage
                ];
                $logData['status'] = 'Lead';
                $logData['remark'] = 'Lead qualified and assigned to bank.';
                break;
            case 'proceed_to_bank_assign':
                if ($lead->lead_type === 'Loan') {
                    $data['status'] = 'Bank Assign';
                    $logData['status'] = 'Bank Assign';
                    $logData['remark'] = 'Moved to Bank Assignment stage (Loan).';
                } elseif ($lead->lead_type === 'RTO') {
                    $data['status'] = 'Lead';
                    $data['rto_stage'] = 'New';
                    $logData['status'] = 'Lead';
                    $logData['remark'] = 'Moved to RTO Module.';
                } elseif ($lead->lead_type === 'Insurance') {
                    $data['status'] = 'Lead';
                    $data['insurance_lead_stage'] = 'New';
                    $logData['status'] = 'Lead';
                    $logData['remark'] = 'Moved to Insurance Module.';
                } else {
                    $data['status'] = 'Bank Assign';
                    $logData['status'] = 'Bank Assign';
                    $logData['remark'] = 'Moved to Bank Assignment stage.';
                }
                break;
            case 'reject':
                $data['status'] = 'Rejected';
                $data['remark'] = $validated['remark'];
                $logData['status'] = 'Rejected';
                $logData['remark'] = $validated['remark'];
                break;
        }

        $lead->update($data);
        $lead->statusLogs()->create($logData);

        return response()->json(['message' => 'Lead status updated successfully.', 'lead' => $lead->fresh()]);
    }
}
