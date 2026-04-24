<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\ReferralLog;
use App\Models\ReferralPerson;
use App\Models\User;
use App\Models\VehicleManufacture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InsuranceLeadController extends Controller
{
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

    private function normalizePremiumData(Request $request, array &$data, ?Lead $existingLead = null): void
    {
        if (($data['premium_amount_collected'] ?? ($existingLead?->premium_amount_collected ?? 'No')) === 'No') {
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
            $type = $data['premium_payment_type'] ?? ($existingLead?->premium_payment_type ?? null);
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
            if ($existingLead?->premium_cheque_image_path) {
                Storage::disk('public')->delete($existingLead->premium_cheque_image_path);
            }
            $data['premium_cheque_image_path'] = $request->file('premium_cheque_image')->store('premium_cheque_images', 'public');
        }
        if ($request->hasFile('premium_online_payment_image')) {
            if ($existingLead?->premium_online_payment_image_path) {
                Storage::disk('public')->delete($existingLead->premium_online_payment_image_path);
            }
            $data['premium_online_payment_image_path'] = $request->file('premium_online_payment_image')->store('premium_online_payment_images', 'public');
        }
        if ($request->hasFile('premium_bank_transfer_image')) {
            if ($existingLead?->premium_bank_transfer_image_path) {
                Storage::disk('public')->delete($existingLead->premium_bank_transfer_image_path);
            }
            $data['premium_bank_transfer_image_path'] = $request->file('premium_bank_transfer_image')->store('premium_bank_transfer_images', 'public');
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lead::whereNotNull('insurance_lead_stage')->with(['currentCity', 'insurance']);

        $this->applyVisibilityScope($query, $user);

        if ($request->filled('status')) {
            $query->where('insurance_lead_status', $request->status);
        }
        if ($request->filled('premium_status')) {
            $query->where('premium_amount_collected', $request->premium_status);
        }

        $leads = $query->latest()->paginate((int) $request->input('per_page', 15));

        return response()->json($leads);
    }

    public function expiring(Request $request)
    {
        $user = Auth::user();

        $query = Lead::whereNotNull('insurance_end_date')
            ->whereBetween('insurance_end_date', [now(), now()->addDays((int) $request->input('days', 30))])
            ->with(['currentCity', 'insurance']);

        $this->applyVisibilityScope($query, $user);

        $leads = $query
            ->orderBy('insurance_end_date', 'asc')
            ->paginate((int) $request->input('per_page', 15));

        return response()->json($leads);
    }

    public function store(Request $request)
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
            'manufacture_id' => 'nullable|exists:vehicle_manufactures,id',
            'model_name' => 'nullable|string|max:255',
            'variant' => 'nullable|string|max:255',
            'insurance_premium_amount' => 'nullable|numeric',
            'insurance_idv_amount' => 'nullable|numeric',
            'premium_amount_collected' => 'required|in:Yes,No',
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
            'insurance_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_front' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_back' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'reporting_manager_id' => 'nullable|exists:users,id',
        ]);

        $data = $validated;
        $actor = Auth::user();

        $data['created_by'] = $actor->id;
        $data['status'] = 'Lead';
        $data['lead_type'] = 'Insurance';
        $data['insurance_lead_stage'] = 'New';
        $data['insurance_lead_status'] = 'In Progress';
        $data['assigned_to'] = $data['assigned_to'] ?? $actor->id;
        $data['reporting_manager_id'] = $data['reporting_manager_id'] ?? $actor->reporting_manager_id;

        if (!empty($data['manufacture_id'])) {
            $mfg = VehicleManufacture::find($data['manufacture_id']);
            $data['mfg_name'] = $mfg?->name;
        }

        if (!empty($data['lead_source_person_name'])) {
            $data['lead_source_person_name'] = $this->normalizeLeadSourcePersonName($data['lead_source_person_name']);
        }

        $this->normalizePremiumData($request, $data);

        $lead = DB::transaction(function () use ($request, $data) {
            $lead = Lead::create($data);

            if ($request->hasFile('insurance_address_proof')) {
                foreach ($request->file('insurance_address_proof') as $file) {
                    $lead->addMedia($file)
                        ->usingName($request->insurance_proof_type ?? 'Address Proof')
                        ->toMediaCollection('insurance_address_proof');
                }
            }
            if ($request->hasFile('aadhar_front')) {
                $lead->addMediaFromRequest('aadhar_front')->toMediaCollection('aadhar_front');
            }
            if ($request->hasFile('aadhar_back')) {
                $lead->addMediaFromRequest('aadhar_back')->toMediaCollection('aadhar_back');
            }

            $lead->statusLogs()->create([
                'status' => 'Lead',
                'remark' => 'Insurance lead created.',
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

        if (!$lead->insurance_lead_stage) {
            return response()->json(['message' => 'This lead is not in the Insurance module.'], 404);
        }

        $lead->load(['insurance', 'vehicleName', 'manufacture', 'statusLogs.user', 'media']);

        return response()->json($lead);
    }

    public function updateDetails(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$this->canAccessLead($user, $lead)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'insurance_id' => 'required|exists:insurances,id',
            'insurance_start_date' => 'required|date',
            'insurance_end_date' => 'required|date',
            'manufacture_id' => 'nullable|exists:vehicle_manufactures,id',
            'model_name' => 'nullable|string|max:255',
            'variant' => 'nullable|string|max:255',
            'vehicle_name_id' => 'nullable|exists:vehicle_names,id',
            'insurance_premium_amount' => 'nullable|numeric',
            'insurance_idv_amount' => 'nullable|numeric',
            'insurance_lead_status' => 'nullable|in:In Progress,Done',
            'premium_amount_collected' => 'required|in:Yes,No',
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
            'pan_card_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'rc_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_front' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_back' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'final_policy_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'insurance_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
        ]);

        $data = $validated;

        if (!empty($data['manufacture_id'])) {
            $mfg = VehicleManufacture::find($data['manufacture_id']);
            $data['mfg_name'] = $mfg?->name;
        }

        if (array_key_exists('lead_source_person_name', $data) && !empty($data['lead_source_person_name'])) {
            $data['lead_source_person_name'] = $this->normalizeLeadSourcePersonName($data['lead_source_person_name']);
        }

        $this->normalizePremiumData($request, $data, $lead);

        if ($request->hasFile('pan_card_document')) {
            $lead->addMediaFromRequest('pan_card_document')->toMediaCollection('insurance_pan_card');
        }
        if ($request->hasFile('rc_document')) {
            $lead->addMediaFromRequest('rc_document')->toMediaCollection('insurance_rc');
        }
        if ($request->hasFile('aadhar_front')) {
            $lead->addMediaFromRequest('aadhar_front')->toMediaCollection('aadhar_front');
        }
        if ($request->hasFile('aadhar_back')) {
            $lead->addMediaFromRequest('aadhar_back')->toMediaCollection('aadhar_back');
        }
        if ($request->hasFile('final_policy_document')) {
            $lead->addMediaFromRequest('final_policy_document')->toMediaCollection('final_document');
        }
        if ($request->hasFile('insurance_address_proof')) {
            foreach ($request->file('insurance_address_proof') as $file) {
                $lead->addMedia($file)->toMediaCollection('insurance_address_proof');
            }
        }

        $lead->update($data);

        return response()->json(['message' => 'Insurance details updated successfully.', 'lead' => $lead->fresh()]);
    }
}
