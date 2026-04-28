<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\ReferralLog;
use App\Models\ReferralPerson;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RtoController extends Controller
{
    protected $stages = [
        'New',
        'Document checking',
        'Agent Assigned',
        'Challan Done',
        'Document Upload',
        'Verification',
        'RTO Approval',
        'RTO Done',
        'Loan Return',
    ];

    private function applyVisibilityScope(Builder $query, User $user): Builder
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        $subordinateIds = User::where('reporting_manager_id', $user->id)->pluck('id');

        return $query->where(function ($q) use ($user, $subordinateIds) {
            $q->where('assigned_to', $user->id)
                ->orWhereIn('assigned_to', $subordinateIds)
                ->orWhere('reporting_manager_id', $user->id);
        });
    }

    private function canAccessLead(User $user, Lead $lead): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        $subordinateIds = User::where('reporting_manager_id', $user->id)->pluck('id');

        return in_array((int) $lead->assigned_to, $subordinateIds->map(fn ($id) => (int) $id)->all(), true)
            || (int) $lead->assigned_to === (int) $user->id
            || (int) $lead->reporting_manager_id === (int) $user->id;
    }

    private function normalizedLeadSource(?string $leadSource): string
    {
        $normalized = strtolower(trim((string) $leadSource));

        return match ($normalized) {
            'referral person', 'any person' => 'referral',
            default => $normalized,
        };
    }

    private function isReferralLeadSource(?string $leadSource): bool
    {
        return $this->normalizedLeadSource($leadSource) === 'referral';
    }

    private function normalizeLeadSourcePersonName(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^(employee|lead|referral):(\d+)$/i', $value, $matches)) {
            $type = strtolower($matches[1]);
            $id = (int) $matches[2];

            return match ($type) {
                'employee' => User::find($id)?->name ?? $value,
                'lead' => Lead::find($id)?->customer_name ?? $value,
                'referral' => ReferralPerson::find($id)?->name ?? $value,
                default => $value,
            };
        }

        return $value;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Lead::with(['vehicleName', 'manufacture', 'rtoAgent'])
            ->where(function ($q) {
                $q->whereNotNull('rto_stage')
                    ->orWhere('lead_type', 'RTO');
            });

        $this->applyVisibilityScope($query, $user);

        if ($request->filled('stage')) {
            $query->where('rto_stage', $request->stage);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leads = $query->latest()->paginate((int) $request->input('per_page', 15));

        return response()->json($leads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'contact_number' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'firm_id' => 'nullable|exists:firms,id',
            'residence_type' => 'nullable|string',
            'current_address_line' => 'nullable|string',
            'current_address_pincode' => 'nullable|string|max:20',
            'current_address_state' => 'nullable|exists:states,id',
            'current_address_city' => 'nullable|exists:cities,id',
            'permanent_address_line' => 'nullable|string',
            'permanent_address_pincode' => 'nullable|string|max:20',
            'permanent_address_state' => 'nullable|exists:states,id',
            'permanent_address_city' => 'nullable|exists:cities,id',
            'rto_rc_number' => 'required|string|max:255',
            'rto_engine_number' => 'nullable|string|max:255',
            'rto_chassis_number' => 'nullable|string|max:255',
            'rto_mfg_year' => 'nullable|string|max:255',
            'rto_mfg_name' => 'nullable|string|max:255',
            'rto_model_name' => 'nullable|string|max:255',
            'rto_variant' => 'nullable|string|max:255',
            'rto_owner_serial_number' => 'nullable|string|max:255',
            'rto_hypothecation_status' => 'nullable|boolean',
            'rto_old_financer' => 'nullable|string|max:255',
            'rto_close_amount' => 'nullable|numeric',
            'rto_emi_paid_count' => 'nullable|integer',
            'assigned_to' => 'nullable|exists:users,id',
            'reporting_manager_id' => 'nullable|exists:users,id',
            'rto_address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'current_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
        ]);

        $actor = Auth::user();
        $data = $validated;
        $data['contact_number'] = Lead::normalizeContactNumber($data['contact_number'] ?? null) ?? ($data['contact_number'] ?? null);

        if ($duplicateLead = Lead::findDuplicateByContact($data['contact_number'] ?? null)) {
            throw ValidationException::withMessages([
                'contact_number' => 'This contact number already exists for lead #' . $duplicateLead->id . ' (' . ($duplicateLead->customer_name ?? 'Unknown') . ').',
            ]);
        }

        $data['created_by'] = $actor->id;
        $data['status'] = 'Lead';
        $data['lead_type'] = 'RTO';
        $data['rto_stage'] = 'New';
        $data['rto_new_at'] = now();
        $data['assigned_to'] = $data['assigned_to'] ?? $actor->id;
        $data['reporting_manager_id'] = $data['reporting_manager_id'] ?? $actor->reporting_manager_id;

        $data['rc_number'] = $data['rto_rc_number'];
        $data['engine_number'] = $data['rto_engine_number'] ?? null;
        $data['chassis_number'] = $data['rto_chassis_number'] ?? null;
        $data['mfg_year'] = $data['rto_mfg_year'] ?? null;
        $data['mfg_name'] = $data['rto_mfg_name'] ?? null;
        $data['model_name'] = $data['rto_model_name'] ?? null;
        $data['variant'] = $data['rto_variant'] ?? null;
        $data['owner_serial_number'] = $data['rto_owner_serial_number'] ?? null;
        $data['hypothecation_status'] = $data['rto_hypothecation_status'] ?? null;
        $data['old_financer'] = $data['rto_old_financer'] ?? null;
        $data['old_loan_close_amount_display'] = $data['rto_close_amount'] ?? null;
        $data['emi_paid_count'] = $data['rto_emi_paid_count'] ?? null;

        if (!empty($data['lead_source_person_name'])) {
            $data['lead_source_person_name'] = $this->normalizeLeadSourcePersonName($data['lead_source_person_name']);
        }

        if ($request->hasFile('rto_address_proof')) {
            $data['rto_address_proof_path'] = $request->file('rto_address_proof')->store('rto_docs', 'public');
        }

        $lead = DB::transaction(function () use ($request, $data) {
            $lead = Lead::create($data);

            if ($request->hasFile('current_address_proof')) {
                foreach ($request->file('current_address_proof') as $file) {
                    $lead->addMedia($file)->toMediaCollection('rto_documents');
                }
            }

            $lead->statusLogs()->create([
                'status' => 'Lead',
                'remark' => 'RTO lead created.',
                'created_by' => Auth::id(),
            ]);

            if ($this->isReferralLeadSource($request->input('lead_source')) && !empty($request->input('lead_source_person_name'))) {
                ReferralLog::firstOrCreate(
                    ['lead_id' => $lead->id],
                    ['amount' => 0, 'status' => 'Pending']
                );
            }

            return $lead;
        });

        return response()->json(['message' => 'New RTO Lead added successfully.', 'lead' => $lead], 201);
    }

    public function storeInsuranceLead(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'father_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'firm_id' => 'nullable|exists:firms,id',
            'residence_type' => 'nullable|string',
            'current_address_line' => 'nullable|string',
            'current_address_pincode' => 'nullable|string|max:20',
            'current_address_state' => 'nullable|exists:states,id',
            'current_address_city' => 'nullable|exists:cities,id',
            'permanent_address_line' => 'nullable|string',
            'permanent_address_pincode' => 'nullable|string|max:20',
            'permanent_address_state' => 'nullable|exists:states,id',
            'permanent_address_city' => 'nullable|exists:cities,id',
            'insurance_address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
        ]);

        $data = $validated;
        $data['contact_number'] = Lead::normalizeContactNumber($data['contact_number'] ?? null) ?? ($data['contact_number'] ?? null);

        if ($duplicateLead = Lead::findDuplicateByContact($data['contact_number'] ?? null)) {
            throw ValidationException::withMessages([
                'contact_number' => 'This contact number already exists for lead #' . $duplicateLead->id . ' (' . ($duplicateLead->customer_name ?? 'Unknown') . ').',
            ]);
        }

        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['lead_type'] = 'Insurance';
        $data['rto_stage'] = 'RTO Done';
        $data['insurance_lead_stage'] = 'New';
        $data['insurance_lead_status'] = 'In Progress';
        $data['assigned_to'] = Auth::id();
        $data['reporting_manager_id'] = Auth::user()->reporting_manager_id;

        if (!empty($data['lead_source_person_name'])) {
            $data['lead_source_person_name'] = $this->normalizeLeadSourcePersonName($data['lead_source_person_name']);
        }

        if ($request->hasFile('insurance_address_proof')) {
            $data['insurance_address_proof_path'] = $request->file('insurance_address_proof')->store('insurance_docs', 'public');
        }

        $lead = DB::transaction(function () use ($request, $data) {
            $lead = Lead::create($data);

            $lead->statusLogs()->create([
                'status' => 'Lead',
                'remark' => 'Insurance lead created from RTO.',
                'created_by' => Auth::id(),
            ]);

            if ($this->isReferralLeadSource($request->input('lead_source')) && !empty($request->input('lead_source_person_name'))) {
                ReferralLog::firstOrCreate(
                    ['lead_id' => $lead->id],
                    ['amount' => 0, 'status' => 'Pending']
                );
            }

            return $lead;
        });

        return response()->json(['message' => 'New Insurance Lead added successfully.', 'lead' => $lead], 201);
    }

    public function show(Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->load(['bank', 'rtoAgent', 'rtoCloserDeposits', 'rtoChallans', 'insurance', 'vehicleName', 'manufacture', 'statusLogs.user']);

        return response()->json($lead);
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|in:Yes,No',
            'current_stage' => 'required|in:' . implode(',', $this->stages),
        ]);

        $currentStageIndex = array_search($request->current_stage, $this->stages, true);

        if ($request->action === 'Yes' && $currentStageIndex !== false && $currentStageIndex < count($this->stages) - 1) {
            $nextStage = $this->stages[$currentStageIndex + 1];
            $lead->update(['rto_stage' => $nextStage]);
            return response()->json(['message' => 'Stage updated to ' . $nextStage, 'lead' => $lead->fresh()]);
        }

        return response()->json(['message' => 'No action taken.', 'lead' => $lead]);
    }

    public function updateVehicleDetail(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rto_vehicle_type' => 'required|in:New Vehicle,Old Vehicle',
            'rto_agent_id' => 'nullable|exists:rto_agents,id',
            'rto_approved_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $data = [
            'rto_vehicle_type' => $request->rto_vehicle_type,
            'old_financer' => $request->old_financer,
            'rto_loan_closed_status' => $request->boolean('rto_loan_closed_status'),
            'rto_hp_termination_status' => $request->boolean('rto_hp_termination_status'),
            'rto_agent_id' => $request->rto_agent_id,
            'rto_challan_verified' => $request->boolean('rto_challan_verified'),
            'rto_verification_status' => $request->boolean('rto_verification_status'),
            'rto_approval_status' => $request->boolean('rto_approval_status'),
        ];

        if ($request->hasFile('rto_approved_doc')) {
            if ($lead->rto_approved_doc_path) {
                Storage::disk('public')->delete($lead->rto_approved_doc_path);
            }
            $data['rto_approved_doc_path'] = $request->file('rto_approved_doc')->store('rto_approved_docs', 'public');
        }

        $lead->update($data);

        if ($request->boolean('save_and_next')) {
            if ($lead->rto_stage === 'Vehicle Detail') {
                $lead->update(['rto_stage' => 'RTO Done']);
            } else {
                $currentStageIndex = array_search($lead->rto_stage, $this->stages, true);
                if ($currentStageIndex !== false && $currentStageIndex < count($this->stages) - 1) {
                    $lead->update(['rto_stage' => $this->stages[$currentStageIndex + 1]]);
                }
            }
        }

        return response()->json(['message' => 'Vehicle details updated successfully.', 'lead' => $lead->fresh()]);
    }

    public function assignAgent(Request $request, Lead $lead)
    {
        $user = Auth::user();
        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['rto_agent_id' => 'required|exists:rto_agents,id']);

        $lead->update([
            'rto_agent_id' => $request->rto_agent_id,
            'rto_agent_assigned_at' => now(),
            'rto_stage' => 'Challan Done',
        ]);

        return response()->json(['message' => 'Agent assigned. Aging started.', 'lead' => $lead->fresh()]);
    }

    public function updateChallan(Request $request, Lead $lead)
    {
        $user = Auth::user();
        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->update([
            'rto_stage' => 'Document Upload',
            'rto_challan_done_at' => now(),
        ]);

        return response()->json(['message' => 'Challan marked as done.', 'lead' => $lead->fresh()]);
    }

    public function uploadRtoDoc(Request $request, Lead $lead)
    {
        $user = Auth::user();
        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['rto_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120']);

        $updateData = [
            'rto_stage' => 'Verification',
            'rto_doc_uploaded_at' => now(),
        ];

        $message = 'Moved to Verification (document upload skipped).';
        if ($request->hasFile('rto_doc')) {
            $path = $request->file('rto_doc')->store('rto_workflow_docs', 'public');
            $updateData['rto_workflow_doc_path'] = $path;
            $message = 'Document uploaded. Moved to Verification.';
        }

        $lead->update($updateData);

        return response()->json(['message' => $message, 'lead' => $lead->fresh()]);
    }

    public function updateVerification(Request $request, Lead $lead)
    {
        $user = Auth::user();
        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->update([
            'rto_stage' => 'RTO Approval',
            'rto_verification_done_at' => now(),
        ]);

        return response()->json(['message' => 'Verification completed.', 'lead' => $lead->fresh()]);
    }

    public function updateApproval(Request $request, Lead $lead)
    {
        $user = Auth::user();
        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->update([
            'rto_stage' => 'RTO Done',
            'rto_approval_status' => true,
            'rto_approval_at' => now(),
        ]);

        return response()->json(['message' => 'RTO Approved.', 'lead' => $lead->fresh()]);
    }

    public function updateRtoDone(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rto_approved_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'action_type' => 'nullable|in:move_to_insurance,stay_rto',
        ]);

        $updateData = [
            'rto_work_done_at' => now(),
            'rto_done_at' => now(),
        ];

        if ($request->hasFile('rto_approved_doc')) {
            if ($lead->rto_approved_doc_path) {
                Storage::disk('public')->delete($lead->rto_approved_doc_path);
            }

            $updateData['rto_approved_doc_path'] = $request->file('rto_approved_doc')->store('rto_final_docs', 'public');
        }

        if ($request->input('action_type') === 'move_to_insurance') {
            $updateData['insurance_lead_stage'] = 'New';
            $updateData['insurance_lead_status'] = 'In Progress';
            $updateData['lead_type'] = 'Insurance';
            $updateData['rto_stage'] = null;
            $message = 'RTO process complete and moved to Insurance.';
        } else {
            $message = 'RTO marked as done successfully.';
        }

        $lead->update($updateData);

        return response()->json(['message' => $message, 'lead' => $lead->fresh()]);
    }

    public function rejectLead(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
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

        return response()->json(['message' => 'Lead rejected and moved to Loan Return.', 'lead' => $lead->fresh()]);
    }
}
