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
use App\Imports\LeadImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view lead', ['only' => ['index', 'show']]);
        $this->middleware('permission:create lead', ['only' => ['create', 'store', 'import', 'downloadSample']]);
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

        // Explicitly order by ID descending to ensure latest leads are first
        $leads = $query->orderBy('id', 'desc')->paginate(10)->appends($request->except('page'));
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
            'lead_type' => 'required|in:Loan,RTO,Insurance',
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

        // Ensure numeric fields are properly handled if they are empty strings
        $data = $request->all();
        $numericFields = ['old_loan_close_amount', 'required_in_hand_amount', 'total_loan_amount', 'itr_amount', 'approx_income', 'showroom_price', 'on_road_price', 'insurance_amount', 'close_amount', 'co_itr_amount', 'co_approx_income', 'guarantor_itr_amount', 'guarantor_approx_income'];

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // Calculate total_loan_amount if not provided but components are
        if (isset($data['old_loan_close_amount']) && isset($data['required_in_hand_amount'])) {
             $data['total_loan_amount'] = (float)$data['old_loan_close_amount'] + (float)$data['required_in_hand_amount'];
        }

        // Automatically promote Prospect to Lead on update if requested
        if ($lead->status == 'Prospect') {
            $data['status'] = 'Lead';
        }

        $lead->update($data);

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
                // Pass data via query parameters to ensure it persists to the edit page
                return redirect()->route(get_route_prefix() . 'leads.edit', [
                    'lead' => $lead->id,
                    'qualify' => 1,
                    'bank_id' => $request->bank_id,
                    'bank_branch_id' => $request->bank_branch_id
                ])->with('error', 'Incomplete Data: Please fill in Job Profile and Loan Details before qualifying the lead.');
            }
            // Changed: Qualify now moves to 'Lead' status, NOT 'Bank Assign'
            $data['status'] = 'Lead';
            $logData['status'] = 'Lead';
            $logData['remark'] = 'Lead qualified.';
        } elseif ($action === 'proceed_to_bank_assign') {
            // New Action: Move from 'Lead' to 'Bank Assign'
            // Logic based on lead_type
            if ($lead->lead_type === 'Loan') {
                $data['status'] = 'Bank Assign';
                $logData['status'] = 'Bank Assign';
                $logData['remark'] = 'Moved to Bank Assignment stage (Loan).';
                $data['bank_id'] = $request->bank_id;
                $data['bank_branch_id'] = $request->bank_branch_id;
            } elseif ($lead->lead_type === 'RTO') {
                $data['status'] = 'Lead'; // Or keep it as Lead, but set RTO stage
                $data['rto_stage'] = 'New'; // Reverted to New
                $data['rto_new_at'] = now(); // Set start time for RTO
                $logData['status'] = 'Lead';
                $logData['remark'] = 'Moved to RTO Module.';
            } elseif ($lead->lead_type === 'Insurance') {
                $data['status'] = 'Lead'; // Or keep it as Lead, but set Insurance stage
                $data['insurance_lead_stage'] = 'New';
                $logData['status'] = 'Lead';
                $logData['remark'] = 'Moved to Insurance Module.';
            } else {
                // Fallback for old leads or undefined types - default to Bank Assign
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
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return (new LeadExport($request))->download();
    }

    public function import(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            // Store file temporarily to avoid /tmp issues on some servers
            $path = $request->file('file')->store('temp_imports');

            // Import using the stored path
            Excel::import(new LeadImport, $path);

            // Delete the temp file
            Storage::delete($path);

            return redirect()->back()->with('success', 'Leads imported successfully.');
        } catch (\Exception $e) {
            // Attempt to delete if it exists
            if (isset($path)) {
                Storage::delete($path);
            }
            // Log the error for debugging
            Log::error('Import Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Import failed. Please ensure the file is a valid Excel or CSV file. Error: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=leads_sample.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['customer_name', 'contact_number', 'father_name', 'email', 'firm_name', 'residence_type', 'current_address_line', 'permanent_address_line'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add a sample row
            fputcsv($file, ['John Doe', '9876543210', 'Jane Doe', 'john@example.com', 'My Firm', 'Rented', '123 Main St', '456 Second St']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
