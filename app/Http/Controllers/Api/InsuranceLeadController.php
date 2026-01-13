<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsuranceLeadController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lead::whereNotNull('insurance_lead_stage');

        // Non-admin users can only see their own leads
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        $leads = $query->latest()->paginate(15);

        return response()->json($leads);
    }

    public function store(Request $request)
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

        if (!$lead->insurance_lead_stage) {
            return response()->json(['message' => 'This lead is not in the Insurance module.'], 404);
        }

        $lead->load(['insurance', 'vehicleName', 'manufacture']);
        return response()->json($lead);
    }

    public function updateDetails(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'insurance_id' => 'required|exists:insurances,id',
            'insurance_start_date' => 'required|date',
            'insurance_end_date' => 'required|date',
            'insurance_type_of_insurance' => 'required|array',
            'insurance_premium_amount' => 'required|numeric',
            'insurance_idv_amount' => 'required|numeric',
        ]);

        $lead->update([
            'insurance_id' => $validated['insurance_id'],
            'insurance_start_date' => $validated['insurance_start_date'],
            'insurance_end_date' => $validated['insurance_end_date'],
            'insurance_type_of_insurance' => implode(', ', $validated['insurance_type_of_insurance']),
            'insurance_premium_amount' => $validated['insurance_premium_amount'],
            'insurance_idv_amount' => $validated['insurance_idv_amount'],
        ]);

        return response()->json(['message' => 'Insurance details updated successfully.', 'lead' => $lead]);
    }
}
