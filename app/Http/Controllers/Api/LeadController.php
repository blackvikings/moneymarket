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
use Illuminate\Validation\ValidationException;

class LeadController extends Controller
{
    private function getAssignableUsersFor(User $user): Builder
    {
        if ($user->hasRole('admin')) {
            return User::withoutRole('admin');
        }

        $role = $user->roles()->first();

        if (!$role) {
            return User::whereKey($user->id);
        }

        if (strcasecmp($role->name, 'Tele Caller') === 0) {
            return User::whereKey($user->id);
        }

        return User::whereHas('roles', function ($query) use ($role) {
            $query->where('position', '>=', $role->position);
        });
    }

    private function canAssignLeadTo(User $actor, ?int $assigneeId): bool
    {
        if (!$assigneeId) {
            return true;
        }

        if ((int) $assigneeId === (int) $actor->id) {
            return true;
        }

        return $this->getAssignableUsersFor($actor)
            ->whereKey($assigneeId)
            ->exists();
    }

    private function validateAssignmentOrFail(User $actor, ?int $assigneeId)
    {
        if (!$this->canAssignLeadTo($actor, $assigneeId)) {
            return response()->json([
                'message' => 'You can only assign leads to yourself, users with the same role, or users below your role. Tele Caller can only assign leads to self.',
                'errors' => [
                    'assigned_to' => [
                        'You can only assign leads to yourself, users with the same role, or users below your role. Tele Caller can only assign leads to self.',
                    ],
                ],
            ], 422);
        }

        return null;
    }

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

    private function isSelfLeadSource(?string $leadSource): bool
    {
        return $this->normalizedLeadSource($leadSource) === 'self';
    }

    private function sourceNeedsPersonName(?string $leadSource): bool
    {
        return in_array($this->normalizedLeadSource($leadSource), ['referral', 'other'], true);
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

    private function applySelfLeadAssignment(array &$data): void
    {
        $data['assigned_to'] = Auth::id();
        $data['reporting_manager_id'] = Auth::user()->reporting_manager_id;
    }

    private function mapLeadTypeData(array $data): array
    {
        if (!isset($data['lead_type'])) {
            return $data;
        }

        if ($data['lead_type'] === 'RTO') {
            $data['rc_number'] = $data['rto_rc_number'] ?? $data['rc_number'] ?? null;
            $data['engine_number'] = $data['rto_engine_number'] ?? $data['engine_number'] ?? null;
            $data['chassis_number'] = $data['rto_chassis_number'] ?? $data['chassis_number'] ?? null;
            $data['mfg_year'] = $data['rto_mfg_year'] ?? $data['mfg_year'] ?? null;
            $data['mfg_name'] = $data['rto_mfg_name'] ?? $data['mfg_name'] ?? null;
            $data['model_name'] = $data['rto_model_name'] ?? $data['model_name'] ?? null;
            $data['variant'] = $data['rto_variant'] ?? $data['variant'] ?? null;
            $data['owner_serial_number'] = $data['rto_owner_serial_number'] ?? $data['owner_serial_number'] ?? null;
            $data['hypothecation_status'] = $data['rto_hypothecation_status'] ?? $data['hypothecation_status'] ?? null;

            if (isset($data['rto_hypothecation_status']) && (int) $data['rto_hypothecation_status'] === 1) {
                $data['old_financer'] = $data['rto_old_financer'] ?? $data['old_financer'] ?? null;
                $data['old_loan_close_amount_display'] = $data['rto_close_amount'] ?? $data['old_loan_close_amount_display'] ?? null;
                $data['emi_paid_count'] = $data['rto_emi_paid_count'] ?? $data['emi_paid_count'] ?? null;
            }
        } elseif ($data['lead_type'] === 'Insurance') {
            $data['mfg_name'] = $data['ins_mfg_name'] ?? $data['mfg_name'] ?? null;
            $data['model_name'] = $data['ins_model_name'] ?? $data['model_name'] ?? null;
            $data['variant'] = $data['ins_variant'] ?? $data['variant'] ?? null;
            $data['owner_serial_number'] = $data['ins_owner_serial_number'] ?? $data['owner_serial_number'] ?? null;
            $data['hypothecation_status'] = $data['ins_hypothecation_status'] ?? $data['hypothecation_status'] ?? null;
            $data['rc_number'] = $data['rto_rc_number'] ?? $data['rc_number'] ?? null;
            $data['engine_number'] = $data['rto_engine_number'] ?? $data['engine_number'] ?? null;
            $data['chassis_number'] = $data['rto_chassis_number'] ?? $data['chassis_number'] ?? null;
        }

        return $data;
    }

    private function normalizePremiumData(Request $request, array &$data): void
    {
        if (($data['lead_type'] ?? null) !== 'Insurance') {
            return;
        }

        if (($data['premium_amount_collected'] ?? 'No') === 'No') {
            $data['premium_payment_type'] = null;
            $data['premium_payment_amount'] = null;
            $data['premium_payment_date'] = null;
            $data['premium_cheque_number'] = null;
            $data['premium_cheque_image_path'] = null;
            $data['premium_upi_id'] = null;
            $data['premium_online_payment_image_path'] = null;
            $data['premium_account_holder_name'] = null;
            $data['premium_account_number'] = null;
            $data['premium_bank_name'] = null;
            $data['premium_ifsc_code'] = null;
            $data['premium_bank_transfer_image_path'] = null;
        } else {
            $type = $data['premium_payment_type'] ?? null;
            if ($type !== 'Cheque') {
                $data['premium_cheque_number'] = null;
                $data['premium_cheque_image_path'] = null;
            }
            if ($type !== 'Online') {
                $data['premium_upi_id'] = null;
                $data['premium_online_payment_image_path'] = null;
            }
            if ($type !== 'Bank Transfer') {
                $data['premium_account_holder_name'] = null;
                $data['premium_account_number'] = null;
                $data['premium_bank_name'] = null;
                $data['premium_ifsc_code'] = null;
                $data['premium_bank_transfer_image_path'] = null;
            }
        }

        if ($request->hasFile('premium_cheque_image')) {
            $data['premium_cheque_image_path'] = $request->file('premium_cheque_image')
                ->store('premium_cheque_images', 'public');
        }
        if ($request->hasFile('premium_online_payment_image')) {
            $data['premium_online_payment_image_path'] = $request->file('premium_online_payment_image')
                ->store('premium_online_payment_images', 'public');
        }
        if ($request->hasFile('premium_bank_transfer_image')) {
            $data['premium_bank_transfer_image_path'] = $request->file('premium_bank_transfer_image')
                ->store('premium_bank_transfer_images', 'public');
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lead::with(['assignedTo', 'bank', 'currentCity']);

        $this->applyVisibilityScope($query, $user);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $leads = $query
            ->orderBy('created_at', 'desc')
            ->paginate((int) $request->input('per_page', 15));

        return response()->json($leads);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'firm_id' => 'nullable|exists:firms,id',
            'residence_type' => 'nullable|string',
            'current_address_line' => 'nullable|string',
            'permanent_address_line' => 'nullable|string',
            'current_address_state' => 'nullable|exists:states,id',
            'current_address_city' => 'nullable|exists:cities,id',
            'permanent_address_state' => 'nullable|exists:states,id',
            'permanent_address_city' => 'nullable|exists:cities,id',
            'lead_type' => 'nullable|in:Loan,RTO,Insurance',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'reporting_manager_id' => 'nullable|exists:users,id',
            'profile_picture' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'proof_id_type' => 'nullable|string|max:255',
            'premium_amount_collected' => 'required_if:lead_type,Insurance|in:Yes,No',
            'premium_payment_type' => 'required_if:premium_amount_collected,Yes|in:Cash,Cheque,Online,Bank Transfer',
            'premium_payment_amount' => 'required_if:premium_payment_type,Cash,Cheque,Online,Bank Transfer|nullable|numeric',
            'premium_payment_date' => 'required_if:premium_amount_collected,Yes|date',
            'premium_cheque_number' => 'required_if:premium_payment_type,Cheque|nullable|string|max:255',
            'premium_cheque_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premium_upi_id' => 'required_if:premium_payment_type,Online|nullable|string|max:255',
            'premium_online_payment_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premium_account_holder_name' => 'required_if:premium_payment_type,Bank Transfer|nullable|string|max:255',
            'premium_account_number' => 'required_if:premium_payment_type,Bank Transfer|nullable|string|max:255',
            'premium_bank_name' => 'required_if:premium_payment_type,Bank Transfer|nullable|string|max:255',
            'premium_ifsc_code' => 'required_if:premium_payment_type,Bank Transfer|nullable|string|max:255',
            'premium_bank_transfer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'current_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'itr_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_proof_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'salary_slip' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'agri_land_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $data = $request->all();
        $actor = Auth::user();

        if ($this->isSelfLeadSource($data['lead_source'] ?? null)) {
            $this->applySelfLeadAssignment($data);
        }

        if ($response = $this->validateAssignmentOrFail($actor, isset($data['assigned_to']) ? (int) $data['assigned_to'] : null)) {
            return $response;
        }

        if (!$this->sourceNeedsPersonName($data['lead_source'] ?? null)) {
            $data['lead_source_person_name'] = null;
        }

        if ($this->sourceNeedsPersonName($data['lead_source'] ?? null) && empty(trim((string) ($data['lead_source_person_name'] ?? '')))) {
            throw ValidationException::withMessages([
                'lead_source_person_name' => 'Please enter the source name.',
            ]);
        }

        if ($this->sourceNeedsPersonName($data['lead_source'] ?? null)) {
            $data['lead_source_person_name'] = $this->normalizeLeadSourcePersonName($data['lead_source_person_name'] ?? null);
        }

        if (!$this->isSelfLeadSource($data['lead_source'] ?? null) && $actor->can('assign lead')) {
            if (empty($data['assigned_to'])) {
                throw ValidationException::withMessages([
                    'assigned_to' => 'Please select a user to assign the lead to.',
                ]);
            }

            if (empty($data['reporting_manager_id'])) {
                throw ValidationException::withMessages([
                    'reporting_manager_id' => 'Please select a reporting manager.',
                ]);
            }
        }

        $data = $this->mapLeadTypeData($data);
        $this->normalizePremiumData($request, $data);

        $numericFields = [
            'old_loan_close_amount',
            'required_in_hand_amount',
            'total_loan_amount',
            'itr_amount',
            'approx_income',
            'showroom_price',
            'on_road_price',
            'insurance_amount',
            'old_loan_close_amount_display',
            'co_itr_amount',
            'co_approx_income',
            'guarantor_itr_amount',
            'guarantor_approx_income',
            'property_approx_value',
            'last_month_salary',
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        if (isset($data['old_loan_close_amount_display']) && isset($data['required_in_hand_amount'])) {
            $data['total_loan_amount'] = (float) $data['old_loan_close_amount_display'] + (float) $data['required_in_hand_amount'];
        }

        $data['created_by'] = $actor->id;
        $data['status'] = 'New';

        if (($data['lead_type'] ?? null) === 'Insurance') {
            $data['insurance_lead_stage'] = 'New';
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture_path'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        if ($request->hasFile('itr_document')) {
            $data['itr_document_path'] = $request->file('itr_document')->store('itr_docs', 'public');
        }
        if ($request->hasFile('business_proof_document')) {
            $data['business_proof_document_path'] = $request->file('business_proof_document')->store('business_proofs', 'public');
        }
        if ($request->hasFile('agri_land_document')) {
            $data['agri_land_document_path'] = $request->file('agri_land_document')->store('agri_land_documents', 'public');
        }

        $lead = DB::transaction(function () use ($request, $data) {
            $lead = Lead::create($data);

            if ($request->hasFile('salary_slip')) {
                $lead->addMedia($request->file('salary_slip'))
                    ->usingName('Salary Slip')
                    ->toMediaCollection('salary_slip');
            }

            if ($request->hasFile('current_address_proof')) {
                foreach ($request->file('current_address_proof') as $file) {
                    $lead->addMedia($file)
                        ->usingName($request->proof_id_type ?? 'Address Proof')
                        ->toMediaCollection('loan_documents');
                }
            }

            $lead->statusLogs()->create([
                'status' => 'New',
                'remark' => 'Lead created.',
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

        return response()->json([
            'message' => 'Lead created successfully.',
            'lead' => $lead->load(['assignedTo', 'statusLogs']),
        ], 201);
    }

    public function show(Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->load(['assignedTo', 'createdBy', 'bank', 'bankBranch', 'firm', 'manufacture', 'vehicleName', 'insurance', 'statusLogs.user']);

        return response()->json($lead);
    }

    public function update(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'customer_name' => 'sometimes|required|string|max:255',
            'father_name' => 'sometimes|nullable|string|max:255',
            'firm_id' => 'sometimes|nullable|exists:firms,id',
            'contact_number' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|nullable|email|max:255',
            'residence_type' => 'sometimes|nullable|string',
            'lead_type' => 'sometimes|nullable|in:Loan,RTO,Insurance',
            'current_address_line' => 'sometimes|nullable|string',
            'current_address_pincode' => 'sometimes|nullable|string|max:20',
            'current_address_state' => 'sometimes|nullable|exists:states,id',
            'current_address_city' => 'sometimes|nullable|exists:cities,id',
            'permanent_address_line' => 'sometimes|nullable|string',
            'permanent_address_pincode' => 'sometimes|nullable|string|max:20',
            'permanent_address_state' => 'sometimes|nullable|exists:states,id',
            'permanent_address_city' => 'sometimes|nullable|exists:cities,id',
            'lead_source' => 'sometimes|nullable|string|max:255',
            'lead_source_person_name' => 'sometimes|nullable|string|max:255',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'reporting_manager_id' => 'sometimes|nullable|exists:users,id',
            'itr_document' => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_proof_document' => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'profile_picture' => 'sometimes|nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'premium_amount_collected' => 'sometimes|nullable|in:Yes,No',
            'premium_payment_type' => 'sometimes|nullable|in:Cash,Cheque,Online,Bank Transfer',
            'premium_payment_amount' => 'sometimes|nullable|numeric',
            'premium_payment_date' => 'sometimes|nullable|date',
            'premium_cheque_number' => 'sometimes|nullable|string|max:255',
            'premium_cheque_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premium_upi_id' => 'sometimes|nullable|string|max:255',
            'premium_online_payment_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premium_account_holder_name' => 'sometimes|nullable|string|max:255',
            'premium_account_number' => 'sometimes|nullable|string|max:255',
            'premium_bank_name' => 'sometimes|nullable|string|max:255',
            'premium_ifsc_code' => 'sometimes|nullable|string|max:255',
            'premium_bank_transfer_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $validated;

        if (array_key_exists('assigned_to', $data)) {
            $assignee = $data['assigned_to'] === null || $data['assigned_to'] === '' ? null : (int) $data['assigned_to'];
            if ($response = $this->validateAssignmentOrFail($user, $assignee)) {
                return $response;
            }
        }

        if ($this->isSelfLeadSource($data['lead_source'] ?? $lead->lead_source)) {
            $this->applySelfLeadAssignment($data);
        }

        if (array_key_exists('lead_source', $data) || array_key_exists('lead_source_person_name', $data)) {
            $effectiveSource = $data['lead_source'] ?? $lead->lead_source;
            $effectivePerson = $data['lead_source_person_name'] ?? $lead->lead_source_person_name;

            if (!$this->sourceNeedsPersonName($effectiveSource)) {
                $data['lead_source_person_name'] = null;
            } elseif (empty(trim((string) $effectivePerson))) {
                throw ValidationException::withMessages([
                    'lead_source_person_name' => 'Please enter the source name.',
                ]);
            } else {
                $data['lead_source_person_name'] = $this->normalizeLeadSourcePersonName((string) $effectivePerson);
            }
        }

        $data = $this->mapLeadTypeData($data);
        $this->normalizePremiumData($request, $data);

        if ($request->hasFile('profile_picture')) {
            if ($lead->profile_picture_path) {
                Storage::disk('public')->delete($lead->profile_picture_path);
            }
            $data['profile_picture_path'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        if ($request->hasFile('itr_document')) {
            if ($lead->itr_document_path) {
                Storage::disk('public')->delete($lead->itr_document_path);
            }
            $data['itr_document_path'] = $request->file('itr_document')->store('itr_docs', 'public');
        }

        if ($request->hasFile('business_proof_document')) {
            if ($lead->business_proof_document_path) {
                Storage::disk('public')->delete($lead->business_proof_document_path);
            }
            $data['business_proof_document_path'] = $request->file('business_proof_document')->store('business_proofs', 'public');
        }

        if ($lead->status === 'Prospect' && $request->boolean('promote_to_lead')) {
            $data['status'] = 'Lead';
        }

        $lead->update($data);

        if ($this->isReferralLeadSource($request->input('lead_source', $lead->lead_source)) && !empty($request->input('lead_source_person_name', $lead->lead_source_person_name))) {
            ReferralLog::firstOrCreate(
                ['lead_id' => $lead->id],
                ['amount' => 0, 'status' => 'Pending']
            );
        }

        return response()->json(['message' => 'Lead updated successfully.', 'lead' => $lead->fresh()]);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'action' => 'required|in:interested,not_interested,follow_up,qualify,proceed_to_bank_assign,reject,mark_active',
            'remark' => 'required_if:action,not_interested|required_if:action,follow_up|required_if:action,reject|required_if:action,mark_active|nullable|string',
            'follow_up_date' => 'required_if:action,follow_up|nullable|date',
            'follow_up_time' => 'required_if:action,follow_up|nullable',
            'bank_id' => 'required_if:action,qualify|required_if:action,proceed_to_bank_assign|nullable|exists:banks,id',
            'bank_branch_id' => 'required_if:action,qualify|required_if:action,proceed_to_bank_assign|nullable|exists:bank_branches,id',
            'bank_executive_name' => 'nullable|string|max:255',
            'new_status' => 'required_if:action,mark_active|nullable|in:New,Prospect',
            'not_active' => 'nullable|boolean',
        ]);

        $action = $validated['action'];
        $data = [];
        $logData = ['created_by' => $user->id];

        if ($action === 'interested') {
            $data['status'] = 'Prospect';
            $logData['status'] = 'Prospect';
            $logData['remark'] = 'Marked as Interested.';
        } elseif ($action === 'not_interested') {
            $data['status'] = 'Not_Active';
            $data['remark'] = $validated['remark'];
            $logData['status'] = 'Not_Active';
            $logData['remark'] = $validated['remark'];
        } elseif ($action === 'follow_up') {
            $data = [
                'status' => 'Prospect',
                'follow_up_date' => $validated['follow_up_date'],
                'follow_up_time' => $validated['follow_up_time'],
                'remark' => $validated['remark'],
            ];
            $logData['status'] = 'Prospect';
            $logData['remark'] = 'Follow-up set: ' . $validated['remark'];

            if (($validated['not_active'] ?? false) === true) {
                $data['status'] = 'Not_Active';
                $logData['status'] = 'Not_Active';
                $logData['remark'] .= ' (Marked as Not Active)';
            }
        } elseif ($action === 'qualify') {
            if (empty($lead->job_profile_type) || empty($lead->loan_type)) {
                return response()->json([
                    'message' => 'Incomplete Data: Please fill in Job Profile and Loan Details before qualifying the lead.',
                ], 422);
            }

            $data['status'] = 'Lead';
            $data['bank_id'] = $validated['bank_id'];
            $data['bank_branch_id'] = $validated['bank_branch_id'];
            $data['converted_at'] = now();
            $logData['status'] = 'Lead';
            $logData['remark'] = 'Lead qualified.';
        } elseif ($action === 'proceed_to_bank_assign') {
            if ($lead->lead_type === 'Loan') {
                $data['status'] = 'Bank Assign';
                $data['bank_id'] = $validated['bank_id'];
                $data['bank_branch_id'] = $validated['bank_branch_id'];
                $data['bank_executive_name'] = $validated['bank_executive_name'] ?? null;
                $data['crm_stage'] = 'New';
                $logData['status'] = 'Bank Assign';
                $logData['remark'] = 'Moved to Bank Assignment stage (Loan).';
            } elseif ($lead->lead_type === 'RTO') {
                $data['status'] = 'Lead';
                $data['rto_stage'] = 'New';
                $data['rto_new_at'] = now();
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
        } elseif ($action === 'reject') {
            $data['status'] = 'Rejected';
            $data['remark'] = $validated['remark'];
            $logData['status'] = 'Rejected';
            $logData['remark'] = $validated['remark'];
        } elseif ($action === 'mark_active') {
            $data['status'] = $validated['new_status'];
            $logData['status'] = $validated['new_status'];
            $logData['remark'] = 'Lead marked as active. ' . $validated['remark'];
        } else {
            return response()->json(['message' => 'Invalid action.'], 422);
        }

        $lead->update($data);
        $lead->statusLogs()->create($logData);

        return response()->json(['message' => 'Lead status updated successfully.', 'lead' => $lead->fresh()]);
    }

    public function assignBank(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'bank_branch_id' => 'required|exists:bank_branches,id',
            'bank_executive_name' => 'nullable|string|max:255',
        ]);

        $lead->update([
            'status' => 'Lead',
            'bank_id' => $validated['bank_id'],
            'bank_branch_id' => $validated['bank_branch_id'],
            'bank_executive_name' => $validated['bank_executive_name'] ?? null,
            'converted_at' => now(),
            'crm_stage' => 'New',
        ]);

        $lead->statusLogs()->create([
            'status' => 'Lead',
            'remark' => 'Bank assigned. Lead moved to CRM.',
            'created_by' => $user->id,
        ]);

        return response()->json(['message' => 'Bank assigned successfully. Lead moved to CRM.', 'lead' => $lead->fresh()]);
    }

    public function assignableUsers()
    {
        $users = $this->getAssignableUsersFor(Auth::user())
            ->select('id', 'name', 'reporting_manager_id')
            ->get();

        return response()->json($users);
    }

    public function managersByUser(User $user)
    {
        $role = $user->roles()->first();

        if (!$role) {
            return response()->json([]);
        }

        $managers = User::where('id', '!=', $user->id)
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('position', '<=', $role->position);
            })
            ->select('id', 'name')
            ->get();

        return response()->json($managers);
    }

    public function searchLeadSourcePersons(Request $request)
    {
        $query = $request->input('q');
        $results = [];

        if (!$query) {
            return response()->json($results);
        }

        $employees = User::where('name', 'like', '%' . $query . '%')
            ->orWhere('employee_id', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name', 'employee_id']);

        foreach ($employees as $emp) {
            $results[] = [
                'id' => 'employee:' . $emp->id,
                'text' => $emp->name . ' (Employee - ' . $emp->employee_id . ')',
                'type' => 'Employee',
                'name' => $emp->name,
                'value' => $emp->name,
            ];
        }

        $pastLeads = Lead::where('customer_name', 'like', '%' . $query . '%')
            ->orWhere('contact_number', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'customer_name', 'contact_number']);

        foreach ($pastLeads as $item) {
            $results[] = [
                'id' => 'lead:' . $item->id,
                'text' => $item->customer_name . ' (Lead Customer - ' . $item->contact_number . ')',
                'type' => 'Lead',
                'name' => $item->customer_name,
                'value' => $item->customer_name,
            ];
        }

        $referrals = ReferralPerson::where('name', 'like', '%' . $query . '%')
            ->orWhere('contact_number', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name', 'contact_number']);

        foreach ($referrals as $ref) {
            $results[] = [
                'id' => 'referral:' . $ref->id,
                'text' => $ref->name . ' (Referral - ' . $ref->contact_number . ')',
                'type' => 'Referral',
                'name' => $ref->name,
                'value' => $ref->name,
            ];
        }

        return response()->json($results);
    }

    public function storeLeadSourcePerson(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $person = ReferralPerson::create([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'address' => $request->address,
            'unique_id' => 'REF' . time(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Referral person created successfully.',
            'person' => $person,
        ], 201);
    }
}
