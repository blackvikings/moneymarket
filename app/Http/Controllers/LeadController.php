<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Bank;
use App\Models\Firm;
use App\Models\VehicleManufacture;
use App\Models\Insurance;
use App\Models\LeadStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\LeadExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view lead', ['only' => ['index', 'show']]);
        $this->middleware('permission:create lead', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit lead', ['only' => ['edit', 'update', 'updateStatus']]);
        $this->middleware('permission:delete lead', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Lead::with(['assignedTo', 'bank']);
        $statuses = ['New', 'Prospect', 'Lead', 'Bank Assign', 'Rejected', 'Not_Active'];
        $currentStatus = $request->input('status');

        // Base query for filtering
        $baseQuery = Lead::query();

        if ($request->has('start_date') && $request->start_date != '') {
            $baseQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $baseQuery->whereDate('created_at', '<=', $request->end_date);
        }

        // Calculate counts based on the filtered query
        $counts = [];
        $counts['All'] = $baseQuery->clone()->count();
        foreach ($statuses as $status) {
            $counts[$status] = $baseQuery->clone()->where('status', $status)->count();
        }

        // Main query for fetching paginated leads
        $query = $baseQuery->clone()->with(['assignedTo', 'bank']);

        if ($currentStatus && in_array($currentStatus, $statuses)) {
            $query->where('status', $currentStatus);
        }

        if ($request->has('assigned_to') && $request->assigned_to != '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Team Lead')) {
             $query->where('assigned_to', Auth::id());
        }

        $leads = $query->latest()->paginate(10)->appends($request->except('page'));
        $users = User::role('Tele Caller')->get();

        return view('leads.index', compact('leads', 'users', 'statuses', 'counts'));
    }

    public function create()
    {
        $users = User::role('Tele Caller')->get();
        $firms = Firm::where('is_active', true)->get();
        return view('leads.create', compact('users', 'firms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'father_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'firm_id' => 'required|exists:firms,id',
            'residence_type' => 'required|string',
            'current_address_line' => 'required|string',
            'permanent_address_line' => 'required|string',
            'assigned_to' => [Rule::requiredIf(function () {
                return Auth::user()->hasRole('admin') || Auth::user()->hasRole('Team Lead');
            })],
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['status'] = 'New';

        $lead = Lead::create($data);

        // Log initial status
        $lead->statusLogs()->create([
            'status' => 'New',
            'remark' => 'Lead created.',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route(get_route_prefix() . 'leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['assignedTo', 'createdBy', 'bank', 'bankBranch', 'firm', 'manufacture', 'vehicleName', 'insurance', 'statusLogs.user']);
        $branches = [];
        if ($lead->bank_id) {
            $bank = Bank::find($lead->bank_id);
            if ($bank) {
                $branches = $bank->branches;
            }
        }
        return view('leads.show', compact('lead', 'branches'));
    }

    public function edit(Lead $lead)
    {
        $users = User::role('Tele Caller')->get();
        $firms = Firm::where('is_active', true)->get();
        $manufactures = VehicleManufacture::where('is_active', true)->get();
        $insurances = Insurance::where('is_active', true)->get();
        $banks = Bank::where('is_active', true)->get();
        return view('leads.edit', compact('lead', 'users', 'firms', 'manufactures', 'insurances', 'banks'));
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            // ... all other detailed validation rules
        ]);

        $lead->update($request->all());

        if ($request->input('qualify_on_save') == 1) {
            $request->validate([
                'bank_id' => 'required|exists:banks,id',
                'bank_branch_id' => 'required|exists:bank_branches,id',
            ]);

            $lead->update([
                'status' => 'Lead',
                'bank_id' => $request->bank_id,
                'bank_branch_id' => $request->bank_branch_id,
                'converted_at' => now(),
            ]);

            $lead->statusLogs()->create([
                'status' => 'Lead',
                'remark' => 'Lead qualified after data update.',
                'created_by' => Auth::id(),
            ]);

            return redirect()->route(get_route_prefix() . 'leads.index')->with('success', 'Lead details updated and qualified successfully!');
        }

        return redirect()->route(get_route_prefix() . 'leads.index')->with('success', 'Lead updated successfully.');
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
        } elseif ($action === 'qualify') {
            if (empty($lead->job_profile_type) || empty($lead->loan_type)) {
                return redirect()->route(get_route_prefix() . 'leads.edit', $lead->id)
                    ->with('error', 'Incomplete Data: Please fill in Job Profile and Loan Details before qualifying the lead.')
                    ->withInput($request->only('bank_id', 'bank_branch_id'));
            }
            // Changed: Qualify now moves to 'Lead' status, NOT 'Bank Assign'
            $data['status'] = 'Lead';
            $logData['status'] = 'Lead';
            $logData['remark'] = 'Lead qualified.';
        } elseif ($action === 'proceed_to_bank_assign') {
            // New Action: Move from 'Lead' to 'Bank Assign'
            $data['status'] = 'Bank Assign';
            $logData['status'] = 'Bank Assign';
            $logData['remark'] = 'Moved to Bank Assignment stage.';
        } elseif ($action === 'reject') {
            $request->validate(['remark' => 'required|string']);
            $data['status'] = 'Rejected';
            $data['remark'] = $request->remark;
            $logData['status'] = 'Rejected';
            $logData['remark'] = $request->remark;
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
            'status' => 'Lead', // Or maybe keep it as 'Bank Assign' or move to a new status like 'CRM'
            // Actually, usually once bank is assigned, it goes to CRM.
            // Let's assume it stays as 'Lead' status but enters CRM pipeline.
            // Wait, if I set it back to 'Lead', it will show up in the 'Lead' tab again.
            // If the flow is New -> Prospect -> Lead -> Bank Assign -> CRM
            // Then after Bank Assign, it should probably disappear from this list or go to a 'Completed' status?
            // Or maybe 'Lead' status implies it's in CRM?
            // Let's assume for now it stays 'Lead' but with bank details, effectively moving it to CRM module visibility.
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

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route(get_route_prefix() . 'leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function export(Request $request)
    {
        return (new LeadExport($request))->download();
    }
}
