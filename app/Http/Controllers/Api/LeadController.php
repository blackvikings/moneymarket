<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $data = $validated;

        // Convert State and City IDs to names
        $data['current_address_state'] = State::find($validated['current_address_state'])->name;
        $data['current_address_city'] = City::find($validated['current_address_city'])->name;
        $data['permanent_address_state'] = State::find($validated['permanent_address_state'])->name;
        $data['permanent_address_city'] = City::find($validated['permanent_address_city'])->name;

        $data['created_by'] = Auth::id();
        $data['status'] = 'New';

        if (!isset($data['assigned_to'])) {
            $data['assigned_to'] = Auth::id();
        }

        $lead = Lead::create($data);

        // Log initial status
        $lead->statusLogs()->create([
            'status' => 'New',
            'remark' => 'Lead created via API.',
            'created_by' => Auth::id(),
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
            'action' => 'required|in:interested,not_interested,follow_up,qualify,reject',
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
