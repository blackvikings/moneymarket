<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Bank;
use App\Models\RtoAgent;
use App\Models\Insurance;
use App\Models\VehicleManufacture;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Exports\RtoExport;
use Carbon\Carbon;

class RtoController extends Controller
{
    protected $stages = [
        'New',
        'Document checking', // Formerly Work checking
        'Agent Assigned',
        'Challan Done',
        'Document Upload',
        'Verification',
        'RTO Approval',
        'RTO Done',
        'Loan Return'
    ];

    public function __construct()
    {
        $this->middleware('permission:view rto', ['only' => ['index', 'show']]);
        $this->middleware('permission:create rto', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit rto', ['only' => ['updateStage', 'updateChecklist', 'updateWorkChecklist', 'assignAgent', 'updateChallan', 'uploadRtoDoc', 'updateVerification', 'updateApproval', 'updateRtoDone', 'rejectLead']]);
        $this->middleware('permission:export rto', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        // Only show leads that are actually in the RTO workflow.
        // Reaching CRM Disbursal does NOT automatically mean "moved to RTO".
        $query = Lead::with(['vehicleName', 'manufacture', 'rtoAgent'])->where(function($q) {
            $q->whereNotNull('rto_stage')
              ->orWhere('lead_type', 'RTO');
        });

        if ($request->has('stage') && $request->stage != '') {
            $query->where('rto_stage', $request->stage);
        }

        $leads = $query->latest()->paginate(10);
        $stages = $this->stages;

        $baseCountQuery = Lead::where(function($q) {
            $q->whereNotNull('rto_stage')
              ->orWhere('lead_type', 'RTO');
        });

        $counts = [];
        $counts['All'] = $baseCountQuery->count();
        foreach ($stages as $stage) {
            $counts[$stage] = (clone $baseCountQuery)->where('rto_stage', $stage)->count();
        }

        return view('rto.index', compact('leads', 'stages', 'counts'));
    }

    public function create()
    {
        $manufactures = VehicleManufacture::where('is_active', true)->get();
        $insurances = Insurance::where('is_active', true)->get();
        $firms = Firm::where('is_active', true)->get();
        return view('rto.create', compact('manufactures', 'insurances', 'firms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'rto_rc_number' => 'required|string|max:255',
            'insurance_available' => 'required|boolean',
            'insurance_end_date' => 'required_if:insurance_available,1|nullable|date',
        ]);

        if ($request->insurance_available == 1 && $request->insurance_end_date) {
            if (Carbon::parse($request->insurance_end_date)->isPast()) {
                return redirect()->back()->withInput()->withErrors(['insurance_end_date' => 'Insurance has expired. Cannot save RTO lead with expired insurance.']);
            }
        }

        $data = $request->except(['current_address_proof', 'residence_type']);
        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['rto_stage'] = 'New';
        $data['lead_type'] = 'RTO';
        $data['rto_new_at'] = now();

        $data['rc_number'] = $request->rto_rc_number;
        // ... mapping other fields ...

        $lead = Lead::create($data);

        if ($request->hasFile('current_address_proof')) {
            foreach ($request->file('current_address_proof') as $file) {
                $lead->addMedia($file)->toMediaCollection('rto_documents');
            }
        }

        return redirect()->route(get_route_prefix() . 'rto.index')->with('success', 'New RTO Lead added successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['bank', 'rtoAgent', 'rtoCloserDeposits', 'rtoChallans', 'insurance', 'media']);
        $stages = $this->stages;
        $currentStage = $lead->rto_stage ?? 'New';
        $currentStageIndex = array_search($currentStage, $stages);
        $banks = Bank::where('is_active', true)->get();
        $rtoAgents = RtoAgent::where('is_active', true)->get();
        $insurances = Insurance::where('is_active', true)->get();

        return view('rto.show-modal', compact('lead', 'stages', 'currentStageIndex', 'banks', 'rtoAgents', 'insurances'))->render();
    }

    public function updateChecklist(Request $request, Lead $lead)
    {
        // This method was for the OLD Document Checking stage.
        // It might be unused now, or we can leave it if we want to support legacy calls,
        // but the new flow uses updateWorkChecklist logic for "Document checking".

        $data = [
            'rto_hsrp_status' => $this->normalizeRtoChecklistValue($request->rto_hsrp_status),
            'rto_hsrp_remark' => $request->rto_hsrp_remark,
            'rto_insurance_status' => $this->normalizeRtoChecklistValue($request->rto_insurance_status),
            'rto_insurance_remark' => $request->rto_insurance_remark,
            'rto_fitness_status' => $this->normalizeRtoChecklistValue($request->rto_fitness_status),
            'rto_fitness_remark' => $request->rto_fitness_remark,
            'rto_tax_status' => $this->normalizeRtoChecklistValue($request->rto_tax_status),
            'rto_tax_remark' => $request->rto_tax_remark,
            'rto_puc_status' => $this->normalizeRtoChecklistValue($request->rto_puc_status),
            'rto_puc_remark' => $request->rto_puc_remark,
            'rto_echallan_status' => $this->normalizeRtoChecklistValue($request->rto_echallan_status),
            'rto_echallan_remark' => $request->rto_echallan_remark,
            'rto_ncr_status' => $this->normalizeRtoChecklistValue($request->rto_ncr_status),
            'rto_ncr_remark' => $request->rto_ncr_remark,
            'rto_challan_status' => $this->normalizeRtoChecklistValue($request->rto_challan_status),
            'rto_challan_remark' => $request->rto_challan_remark,
            'rto_permit_status' => $this->normalizeRtoChecklistValue($request->rto_permit_status),
            'rto_permit_remark' => $request->rto_permit_remark,
        ];

        $lead->update($data);

        // If for some reason this is called, jump to Agent Assigned as per new flow?
        // But better to use the other method.
        if ($request->has('save_and_next')) {
            $lead->update(['rto_stage' => 'Agent Assigned']);
            return redirect()->back()->with('success', 'Document checklist updated.');
        }

        return redirect()->back()->with('success', 'Checklist updated successfully.');
    }

    public function updateWorkChecklist(Request $request, Lead $lead)
    {
        // This handles the new "Document checking" stage (formerly Work Checking)
        $data = $request->only(['rto_name_transfer_status', 'rto_seller_name', 'rto_seller_number', 'rto_seller_address', 'rto_hypothecation_add_status', 'rto_hypothecation_add_bank_id']);

        // Allow updating all checklist items
        $docFields = ['rto_hsrp_status', 'rto_insurance_status', 'rto_fitness_status', 'rto_tax_status', 'rto_puc_status', 'rto_echallan_status', 'rto_ncr_status', 'rto_challan_status', 'rto_permit_status'];
        foreach($docFields as $field) {
            if ($request->has($field)) {
                $data[$field] = $this->normalizeRtoChecklistValue($request->input($field));
            }
        }

        $lead->update($data);

        if ($request->has('save_and_next')) {
            $lead->update(['rto_stage' => 'Agent Assigned']); // Logic will show Agent Assign form
            return redirect()->back()->with('success', 'Document checklist updated. Please assign an agent.');
        }

        return redirect()->back()->with('success', 'Work details updated.');
    }

    private function rtoChecklistStorageMode(): string
    {
        static $mode = null;
        if ($mode !== null) return $mode;

        $mode = 'string'; // default to the intended representation

        try {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql') {
                $col = DB::selectOne("SHOW COLUMNS FROM `leads` LIKE 'rto_hsrp_status'");
                $type = strtolower((string) ($col->Type ?? ''));
                if ($type !== '' && (str_contains($type, 'tinyint') || str_contains($type, 'int') || str_contains($type, 'bool'))) {
                    $mode = 'numeric';
                }
            } elseif ($driver === 'sqlite') {
                $cols = DB::select("PRAGMA table_info('leads')");
                foreach ($cols as $c) {
                    if (($c->name ?? null) === 'rto_hsrp_status') {
                        $type = strtolower((string) ($c->type ?? ''));
                        if ($type !== '' && (str_contains($type, 'int') || str_contains($type, 'bool'))) {
                            $mode = 'numeric';
                        }
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Keep default 'string' mode if schema introspection fails.
        }

        return $mode;
    }

    private function normalizeRtoChecklistValue(mixed $value): mixed
    {
        $mode = $this->rtoChecklistStorageMode();

        if ($mode === 'numeric') {
            // Numeric mapping:
            // 0 = Pending (legacy false)
            // 1 = Done (legacy true)
            // 2 = Previously Done
            if ($value === null || $value === '' || $value === 0 || $value === '0' || $value === false || $value === 'Pending') return 0;
            if ($value === 2 || $value === '2' || $value === 'Previously Done') return 2;
            if ($value === 1 || $value === '1' || $value === true || $value === 'Done') return 1;
            return 0;
        }

        // String mode: keep the 3-state text values.
        if ($value === 2 || $value === '2') return 'Previously Done';
        if ($value === 1 || $value === '1' || $value === true) return 'Done';
        if ($value === 0 || $value === '0' || $value === false) return 'Pending';
        if ($value === 'Pending' || $value === 'Previously Done' || $value === 'Done') return $value;
        return 'Pending';
    }

    public function assignAgent(Request $request, Lead $lead)
    {
        $request->validate(['rto_agent_id' => 'required|exists:rto_agents,id']);

        $lead->update([
            'rto_agent_id' => $request->rto_agent_id,
            'rto_agent_assigned_at' => now(),
            'rto_stage' => 'Challan Done'
        ]);

        return redirect()->back()->with('success', 'Agent assigned. Aging started.');
    }

    public function updateChallan(Request $request, Lead $lead)
    {
        $lead->update(['rto_stage' => 'Document Upload', 'rto_challan_done_at' => now()]);
        return redirect()->back()->with('success', 'Challan marked as done.');
    }

    public function uploadRtoDoc(Request $request, Lead $lead)
    {
        // Document Upload is optional in the RTO workflow.
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
        return redirect()->back()->with('success', $message);
    }

    public function updateVerification(Request $request, Lead $lead)
    {
        $lead->update(['rto_stage' => 'RTO Approval', 'rto_verification_done_at' => now()]);
        return redirect()->back()->with('success', 'Verification completed.');
    }

    public function updateApproval(Request $request, Lead $lead)
    {
        $lead->update([
            'rto_stage' => 'RTO Done',
            'rto_approval_status' => true,
            'rto_approval_at' => now(),
        ]);
        return redirect()->back()->with('success', 'RTO Approved.');
    }

    public function updateRtoDone(Request $request, Lead $lead)
    {
        $request->validate([
            'rto_approved_doc' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remark' => 'nullable|string',
            'action_type' => 'nullable|in:mark_rto_done,move_to_insurance,save',
        ]);

        $path = $request->file('rto_approved_doc')->store('rto_final_docs', 'public');

        $updateData = [
            'rto_approved_doc_path' => $path,
            'rto_work_done_at' => now(),
            'remark' => $request->input('remark'),
        ];

        if ($request->input('action_type') === 'move_to_insurance') {
            $updateData['insurance_lead_stage'] = 'New';
            $updateData['lead_type'] = 'Insurance'; // Update lead type to Insurance if needed, depending on business logic
            $updateData['rto_stage'] = null;
            $lead->update($updateData);
            return redirect()->back()->with('success', 'Final document uploaded. RTO process complete and moved to Insurance.');
        } else {
            $lead->update($updateData);
            return redirect()->back()->with('success', 'RTO marked as done successfully.');
        }
    }

    public function updateStage(Request $request, Lead $lead)
    {
        // Simple stage mover if needed manually
        $currentIdx = array_search($lead->rto_stage, $this->stages);
        if ($currentIdx !== false && $currentIdx < count($this->stages) - 1) {
            $lead->update(['rto_stage' => $this->stages[$currentIdx + 1]]);
        }
        return redirect()->back();
    }

    public function rejectLead(Request $request, Lead $lead)
    {
        $lead->update(['rto_stage' => 'Loan Return', 'loan_return_reason' => $request->rejection_reason, 'loan_return_date' => now()]);
        return redirect()->back();
    }
}
