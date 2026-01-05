<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $data['rto_stage'] = 'Insurance';
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
}
