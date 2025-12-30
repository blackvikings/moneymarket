<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Bank;
use App\Models\Firm;
use App\Models\VehicleManufacture;
use App\Models\Insurance;
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
        $statuses = ['New', 'Prospect', 'Lead', 'Rejected', 'Not_Active'];
        $currentStatus = $request->input('status');

        if ($currentStatus && in_array($currentStatus, $statuses)) {
            $query->where('status', $currentStatus);
        }

        if ($request->has('assigned_to') && $request->assigned_to != '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Team Lead')) {
             $query->where('assigned_to', Auth::id());
        }

        $leads = $query->latest()->paginate(10);
        $users = User::role('Tele Caller')->get();

        $counts = [];
        $counts['All'] = Lead::count();
        foreach ($statuses as $status) {
            $counts[$status] = Lead::where('status', $status)->count();
        }

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
            'email' => 'required|email|max:255',
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

        Lead::create($data);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['assignedTo', 'createdBy', 'bank', 'bankBranch', 'firm', 'manufacture', 'vehicleName', 'insurance']);
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
        // This remains unchanged for detailed editing
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            // ... all other detailed validation rules
        ]);
        $lead->update($request->all());
        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $action = $request->input('action');

        if ($action === 'interested') {
            $lead->update(['status' => 'Prospect']);
            return redirect()->back()->with('success', 'Lead status updated to Prospect.');
        }

        if ($action === 'not_interested') {
            $request->validate(['remark' => 'required|string']);
            $lead->update(['status' => 'Not_Active', 'remark' => $request->remark]);
            return redirect()->back()->with('success', 'Lead status updated to Not Active.');
        }

        if ($action === 'follow_up') {
            $request->validate([
                'follow_up_date' => 'required|date',
                'follow_up_time' => 'required',
                'remark' => 'required|string',
            ]);
            $lead->update([
                'status' => 'Prospect', // Stays as prospect
                'follow_up_date' => $request->follow_up_date,
                'follow_up_time' => $request->follow_up_time,
                'remark' => $request->remark,
            ]);
            return redirect()->back()->with('success', 'Follow-up has been set.');
        }

        if ($action === 'qualify') {
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
            return redirect()->back()->with('success', 'Lead has been qualified and assigned to the bank.');
        }

        if ($action === 'reject') {
            $request->validate(['remark' => 'required|string']);
            $lead->update(['status' => 'Rejected', 'remark' => $request->remark]);
            return redirect()->back()->with('success', 'Lead has been rejected.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function export(Request $request)
    {
        return (new LeadExport($request))->download();
    }
}
