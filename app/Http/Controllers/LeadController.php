<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Bank;
use App\Models\Firm;
use App\Models\VehicleManufacture;
use App\Models\Insurance;
use App\Models\LeadStatusLog;
use App\Models\ReferralLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\LeadExport;
use App\Imports\LeadImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Media;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view lead', ['only' => ['index', 'show']]);
        $this->middleware('permission:create lead', ['only' => ['create', 'store', 'import', 'downloadSample']]);
        $this->middleware('permission:edit lead', ['only' => ['edit', 'update', 'updateStatus', 'bulkAssign', 'assignBank']]);
        $this->middleware('permission:delete lead', ['only' => ['destroy']]);
        $this->middleware('permission:export lead', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        $statuses = ['New', 'Follow up', 'Lead', 'Bank Assign', 'Rejected', 'Not_Active'];
        $currentStatus = $request->input('status');
        $user = Auth::user();

        // 1. Define Base Query
        $query = Lead::query();

        // 2. Hierarchy Logic
        if ($user->hasRole('Team Lead')) {
            $subordinateIds = User::where('reporting_manager_id', $user->id)->pluck('id')->toArray();
            $subordinateIds[] = $user->id;
            $query->whereIn('assigned_to', $subordinateIds);
        } elseif (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        // 3. Apply Date Filters (Common)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Clone for counts
        $countQuery = clone $query;

        // 4. Apply Specific Filters
        if ($currentStatus && in_array($currentStatus, $statuses)) {
            $dbStatus = ($currentStatus === 'Follow up') ? 'Prospect' : $currentStatus;
            $query->where('status', $dbStatus);
        }

        if ($request->filled('assignment_status')) {
            if ($request->assignment_status === 'Assigned') {
                $query->whereNotNull('assigned_to');
            } elseif ($request->assignment_status === 'Unassigned') {
                $query->whereNull('assigned_to');
            }
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // 5. Calculate Counts
        $counts = ['All' => $countQuery->count()];
        foreach ($statuses as $status) {
            $dbStatus = ($status === 'Follow up') ? 'Prospect' : $status;
            $counts[$status] = (clone $countQuery)->where('status', $dbStatus)->count();
        }

        // 6. Get Results
        $leads = $query->with(['assignedTo', 'bank', 'currentCity'])
            ->orderByRaw('assigned_to IS NULL DESC, id DESC')
            ->paginate(10)
            ->appends($request->except('page'));

        // 7. Users for dropdown
        if ($user->hasRole('admin')) {
            $users = User::all();
        } elseif ($user->hasRole('Team Lead')) {
             $subordinateIds = User::where('reporting_manager_id', $user->id)->pluck('id')->toArray();
             $otherTeamLeadIds = User::role('Team Lead')->where('id', '!=', $user->id)->pluck('id')->toArray();
             $users = User::whereIn('id', array_merge($subordinateIds, $otherTeamLeadIds))->get();
        } else {
            $users = collect([]);
        }

        return view('leads.index', compact('leads', 'users', 'statuses', 'counts'));
    }

    public function create()
    {
        $user = Auth::user();
        $userRole = $user->roles->first();

        $managers = User::whereHas('roles', function ($query) use ($userRole) {
            $query->where('position', '<', $userRole->position);
        })->get();

        if ($user->hasRole('admin')) {
            $users = User::withoutRole('admin')->get();
        } elseif ($user->hasRole('Team Lead')) {
            $subordinateIds = User::where('reporting_manager_id', $user->id)->pluck('id')->toArray();
            $otherTeamLeadIds = User::role('Team Lead')->where('id', '!=', $user->id)->pluck('id')->toArray();
            $users = User::whereIn('id', array_merge($subordinateIds, $otherTeamLeadIds))->get();
        } else {
            $users = collect([$user]);
        }

        $firms = Firm::where('is_active', true)->get();
        $manufactures = VehicleManufacture::where('is_active', true)->get();
        $insurances = Insurance::where('is_active', true)->get();
        $banks = Bank::where('is_active', true)->get();
        return view('leads.create', compact('users', 'firms', 'manufactures', 'insurances', 'banks', 'managers'));
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
            'lead_type' => 'nullable|in:Loan,RTO,Insurance',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'profile_picture' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'proof_id_type' => 'nullable|string|max:255',
            'current_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'itr_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_proof_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_number' => 'nullable|string|max:20',
            'property_approx_value' => 'nullable|numeric',
            'salary_slip' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'agri_land_area' => 'nullable|string|max:255',
            'agri_land_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'job_profile_type' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        if (($data['lead_source'] ?? '') !== 'Any person') {
            $data['lead_source_person_name'] = null;
        }

        $data = $this->mapLeadTypeData($data);

        $numericFields = ['old_loan_close_amount', 'required_in_hand_amount', 'total_loan_amount', 'itr_amount', 'approx_income', 'showroom_price', 'on_road_price', 'insurance_amount', 'old_loan_close_amount_display', 'co_itr_amount', 'co_approx_income', 'guarantor_itr_amount', 'guarantor_approx_income', 'property_approx_value', 'last_month_salary'];

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        if (isset($data['old_loan_close_amount_display']) && isset($data['required_in_hand_amount'])) {
             $data['total_loan_amount'] = (float)$data['old_loan_close_amount_display'] + (float)$data['required_in_hand_amount'];
        }

        $data['created_by'] = Auth::id();
        $data['status'] = 'New';

        if (isset($data['lead_type']) && $data['lead_type'] === 'Insurance') {
            $data['insurance_lead_stage'] = 'New';
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture_path'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        if ($request->hasFile('salary_slip')) {
            $data['salary_slip_path'] = $request->file('salary_slip')->store('salary_slips', 'public');
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

        $lead = Lead::create($data);

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

        if ($request->input('lead_source') === 'Any person' && !empty($request->input('lead_source_person_name'))) {
            ReferralLog::create([
                'lead_id' => $lead->id,
                'amount' => 0,
                'status' => 'Pending',
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Lead created successfully.']);
        }

        return redirect()->route(get_route_prefix() . 'leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['assignedTo', 'createdBy', 'bank', 'bankBranch', 'firm', 'manufacture', 'vehicleName', 'insurance', 'statusLogs.user', 'media']);
        $branches = [];
        if ($lead->bank_id) {
            $bank = Bank::find($lead->bank_id);
            if ($bank) {
                $branches = $bank->branches;
            }
        }
        return view('leads.show-modal', compact('lead', 'branches'))->render();
    }

    public function edit(Lead $lead)
    {
        $user = Auth::user();
        $userRole = $user->roles->first();

        $managers = User::whereHas('roles', function ($query) use ($userRole) {
            $query->where('position', '<', $userRole->position);
        })->get();

        if ($user->hasRole('admin')) {
            $users = User::all();
        } elseif ($user->hasRole('Team Lead')) {
            $subordinateIds = User::where('reporting_manager_id', $user->id)->pluck('id')->toArray();
            $otherTeamLeadIds = User::role('Team Lead')->where('id', '!=', $user->id)->pluck('id')->toArray();
            $users = User::whereIn('id', array_merge($subordinateIds, $otherTeamLeadIds))->get();
        } else {
            $users = User::where('id', $lead->assigned_to)->get();
        }

        $firms = Firm::where('is_active', true)->get();
        $manufactures = VehicleManufacture::where('is_active', true)->get();
        $insurances = Insurance::where('is_active', true)->get();
        $banks = Bank::where('is_active', true)->get();
        return view('leads.edit', compact('lead', 'users', 'firms', 'manufactures', 'insurances', 'banks', 'managers'));
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'profile_picture' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'proof_id_type' => 'nullable|string|max:255',
            'current_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'itr_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_proof_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_number' => 'nullable|string|max:20',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
            'property_approx_value' => 'nullable|numeric',
            'salary_slip' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'agri_land_area' => 'nullable|string|max:255',
            'agri_land_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'job_profile_type' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        if (($data['lead_source'] ?? '') !== 'Any person') {
            $data['lead_source_person_name'] = null;
        }

        $data = $this->mapLeadTypeData($data);

        $numericFields = ['old_loan_close_amount', 'required_in_hand_amount', 'total_loan_amount', 'itr_amount', 'approx_income', 'showroom_price', 'on_road_price', 'insurance_amount', 'old_loan_close_amount_display', 'co_itr_amount', 'co_approx_income', 'guarantor_itr_amount', 'guarantor_approx_income', 'property_approx_value', 'last_month_salary'];

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        if (isset($data['old_loan_close_amount_display']) && isset($data['required_in_hand_amount'])) {
             $data['total_loan_amount'] = (float)$data['old_loan_close_amount_display'] + (float)$data['required_in_hand_amount'];
        }

        if ($lead->status == 'Prospect') {
            $data['status'] = 'Lead';
        }

        if (isset($data['lead_type']) && $data['lead_type'] === 'Insurance' && !$lead->insurance_lead_stage) {
            $data['insurance_lead_stage'] = 'New';
        }

        if ($request->hasFile('profile_picture')) {
            if ($lead->profile_picture_path) Storage::disk('public')->delete($lead->profile_picture_path);
            $data['profile_picture_path'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        if ($request->hasFile('salary_slip')) {
            if ($lead->salary_slip_path) Storage::disk('public')->delete($lead->salary_slip_path);
            $data['salary_slip_path'] = $request->file('salary_slip')->store('salary_slips', 'public');
        }

        if ($request->hasFile('itr_document')) {
            if ($lead->itr_document_path) Storage::disk('public')->delete($lead->itr_document_path);
            $data['itr_document_path'] = $request->file('itr_document')->store('itr_docs', 'public');
        }

        if ($request->hasFile('business_proof_document')) {
            if ($lead->business_proof_document_path) Storage::disk('public')->delete($lead->business_proof_document_path);
            $data['business_proof_document_path'] = $request->file('business_proof_document')->store('business_proofs', 'public');
        }

        if ($request->hasFile('agri_land_document')) {
            if ($lead->agri_land_document_path) Storage::disk('public')->delete($lead->agri_land_document_path);
            $data['agri_land_document_path'] = $request->file('agri_land_document')->store('agri_land_documents', 'public');
        }

        if ($request->hasFile('current_address_proof')) {
            foreach ($request->file('current_address_proof') as $file) {
                $lead->addMedia($file)
                     ->usingName($request->proof_id_type ?? 'Address Proof')
                     ->toMediaCollection('loan_documents');
            }
        }

        if ($request->input('qualify_on_save') == 1) {
            $request->validate([
                'bank_id' => 'required|exists:banks,id',
                'bank_branch_id' => 'required|exists:bank_branches,id',
            ]);

            $data['status'] = 'Lead';
            $data['bank_id'] = $request->bank_id;
            $data['bank_branch_id'] = $request->bank_branch_id;
            $data['converted_at'] = now();

            $lead->statusLogs()->create([
                'status' => 'Lead',
                'remark' => 'Lead qualified after data update.',
                'created_by' => Auth::id(),
            ]);
        }

        $lead->update($data);

        if ($request->input('lead_source') === 'Any person' && !empty($request->input('lead_source_person_name'))) {
            ReferralLog::firstOrCreate(
                ['lead_id' => $lead->id],
                ['amount' => 0, 'status' => 'Pending']
            );
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Lead updated successfully.']);
        }

        return redirect()->route(get_route_prefix() . 'leads.index')->with('success', 'Lead updated successfully.');
    }

    public function deleteMedia($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        $media->delete();
        return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $action = $request->input('action');
        $data = [];
        $logData = ['created_by' => Auth::id()];

        if ($action === 'interested') {
            $data['status'] = 'Prospect';
            $logData['status'] = 'Prospect';
            $logData['remark'] = 'Marked as Interested.';
        } elseif ($action === 'not_interested') {
            $request->validate(['remark' => 'required|string']);
            $data['status'] = 'Not_Active';
            $data['remark'] = $request->remark;
            $logData['status'] = 'Not_Active';
            $logData['remark'] = $request->remark;
        } elseif ($action === 'follow_up') {
            $request->validate([
                'follow_up_date' => 'required|date',
                'follow_up_time' => 'required',
                'remark' => 'required|string',
            ]);
            $data = [
                'status' => 'Prospect',
                'follow_up_date' => $request->follow_up_date,
                'follow_up_time' => $request->follow_up_time,
                'remark' => $request->remark,
            ];
            $logData['status'] = 'Prospect';
            $logData['remark'] = 'Follow-up set: ' . $request->remark;

            if ($request->has('not_active') && $request->not_active == 1) {
                $data['status'] = 'Not_Active';
                $logData['status'] = 'Not_Active';
                $logData['remark'] .= ' (Marked as Not Active)';
            }
        } elseif ($action === 'qualify') {
            if (empty($lead->job_profile_type) || empty($lead->loan_type)) {
                return redirect()->route(get_route_prefix() . 'leads.edit', [
                    'lead' => $lead->id,
                    'qualify' => 1,
                    'bank_id' => $request->bank_id,
                    'bank_branch_id' => $request->bank_branch_id
                ])->with('error', 'Incomplete Data: Please fill in Job Profile and Loan Details before qualifying the lead.');
            }
            $data['status'] = 'Lead';
            $logData['status'] = 'Lead';
            $logData['remark'] = 'Lead qualified.';
        } elseif ($action === 'proceed_to_bank_assign') {
            if ($lead->lead_type === 'Loan') {
                $data['status'] = 'Bank Assign';
                $logData['status'] = 'Bank Assign';
                $logData['remark'] = 'Moved to Bank Assignment stage (Loan).';
                $data['bank_id'] = $request->bank_id;
                $data['bank_branch_id'] = $request->bank_branch_id;
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
            $request->validate(['remark' => 'required|string']);
            $data['status'] = 'Rejected';
            $data['remark'] = $request->remark;
            $logData['status'] = 'Rejected';
            $logData['remark'] = $request->remark;
        } elseif ($action === 'mark_active') {
            $request->validate([
                'new_status' => 'required|in:New,Prospect',
                'remark' => 'required|string'
            ]);
            $data['status'] = $request->new_status;
            $logData['status'] = $request->new_status;
            $logData['remark'] = 'Lead marked as active. ' . $request->remark;
        } else {
            return redirect()->back()->with('error', 'Invalid action.');
        }

        $lead->update($data);
        $lead->statusLogs()->create($logData);

        return redirect()->back()->with('success', 'Lead status updated successfully.');
    }

    public function assignBank(Request $request, Lead $lead)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'bank_branch_id' => 'required|exists:bank_branches,id',
        ]);

        $lead->update([
            'status' => 'Lead',
            'bank_id' => $request->bank_id,
            'bank_branch_id' => $request->bank_branch_id,
            'converted_at' => now(),
            'crm_stage' => 'New',
        ]);

        $lead->statusLogs()->create([
            'status' => 'Lead',
            'remark' => 'Bank assigned. Lead moved to CRM.',
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Bank assigned successfully. Lead moved to CRM.');
    }

    public function bulkAssign(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('bulk assign lead')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $leadIds = $request->input('lead_ids');
        $assignedTo = $request->input('assigned_to');

        Lead::whereIn('id', $leadIds)->update(['assigned_to' => $assignedTo]);

        foreach ($leadIds as $leadId) {
            LeadStatusLog::create([
                'lead_id' => $leadId,
                'status' => 'Assigned',
                'remark' => 'Bulk assigned to user ID ' . $assignedTo,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'Leads assigned successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route(get_route_prefix() . 'leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('export lead')) {
            abort(403, 'Unauthorized action.');
        }
        return (new LeadExport($request))->download();
    }

    public function import(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('import lead')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $path = $request->file('file')->store('temp_imports');
            Excel::import(new LeadImport, $path);
            Storage::delete($path);
            return redirect()->back()->with('success', 'Leads imported successfully.');
        } catch (\Exception $e) {
            if (isset($path)) Storage::delete($path);
            Log::error('Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Import failed. Error: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('import lead')) {
            abort(403, 'Unauthorized action.');
        }
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=leads_sample.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['customer_name', 'contact_number', 'father_name', 'email', 'firm_name', 'residence_type', 'current_address_line', 'permanent_address_line', 'employee_id']);
            fputcsv($file, ['John Doe', '9876543210', 'Jane Doe', 'john@example.com', 'My Firm', 'Rented', '123 Main St', '456 Second St', 'MM26020601']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function mapLeadTypeData($data)
    {
        if (isset($data['lead_type'])) {
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

                if (isset($data['rto_hypothecation_status']) && $data['rto_hypothecation_status'] == 1) {
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

                // MAP Missing fields for Insurance Type
                $data['rc_number'] = $data['rto_rc_number'] ?? $data['rc_number'] ?? null;
                $data['engine_number'] = $data['rto_engine_number'] ?? $data['engine_number'] ?? null;
                $data['chassis_number'] = $data['rto_chassis_number'] ?? $data['chassis_number'] ?? null;
                $data['mfg_year'] = $data['mfg_year'] ?? null; // mfg_year is directly taken from 'mfg_year' name in form
            }
        }
        return $data;
    }
}
