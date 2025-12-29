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

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_to') && $request->assigned_to != '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Team Lead')) {
             $query->where('assigned_to', Auth::id());
        }

        $leads = $query->latest()->paginate(10);
        $users = User::role('Tele Caller')->get();

        return view('leads.index', compact('leads', 'users'));
    }

    public function create()
    {
        $users = User::role('Tele Caller')->get();
        $firms = Firm::where('is_active', true)->get();
        $manufactures = VehicleManufacture::where('is_active', true)->get();
        $insurances = Insurance::where('is_active', true)->get();

        return view('leads.create', compact('users', 'firms', 'manufactures', 'insurances'));
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
            'job_profile_type' => 'required|string',
            'nature_of_work' => ['required_if:job_profile_type,Self Employed'],
            'salaried_type' => ['required_if:job_profile_type,Salaried'],
            'designation' => ['required_if:job_profile_type,Salaried'],
            'other_company_name' => ['required_if:salaried_type,Other'],
            'itr_amount' => ['required_if:income_proof_available,1'],
            'approx_income' => ['required_if:income_proof_available,0'],
            'old_loan_close_amount' => 'required|numeric|min:0',
            'co_name' => ['required_if:has_co_applicant,1'],
            'co_relation' => ['required_if:has_co_applicant,1'],
        ]);

        $data = $request->except(['itr_document', 'business_proof_document', 'co_itr_document', 'co_business_proof_document', 'guarantor_itr_document', 'guarantor_business_proof_document']);
        $data['created_by'] = Auth::id();
        $data['status'] = 'New';

        // File Uploads
        if ($request->hasFile('itr_document')) {
            $data['itr_document_path'] = $request->file('itr_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('business_proof_document')) {
            $data['business_proof_document_path'] = $request->file('business_proof_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('co_itr_document')) {
            $data['co_itr_document_path'] = $request->file('co_itr_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('co_business_proof_document')) {
            $data['co_business_proof_document_path'] = $request->file('co_business_proof_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('guarantor_itr_document')) {
            $data['guarantor_itr_document_path'] = $request->file('guarantor_itr_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('guarantor_business_proof_document')) {
            $data['guarantor_business_proof_document_path'] = $request->file('guarantor_business_proof_document')->store('lead_docs', 'public');
        }

        // Calculate Total Loan Amount
        $oldLoan = $request->input('old_loan_close_amount', 0);
        $inHand = $request->input('required_in_hand_amount', 0);
        $data['total_loan_amount'] = $oldLoan + $inHand;

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
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = $request->except(['itr_document', 'business_proof_document', 'co_itr_document', 'co_business_proof_document', 'guarantor_itr_document', 'guarantor_business_proof_document']);

        // File Uploads (Delete old if new uploaded)
        if ($request->hasFile('itr_document')) {
            if ($lead->itr_document_path) Storage::disk('public')->delete($lead->itr_document_path);
            $data['itr_document_path'] = $request->file('itr_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('business_proof_document')) {
            if ($lead->business_proof_document_path) Storage::disk('public')->delete($lead->business_proof_document_path);
            $data['business_proof_document_path'] = $request->file('business_proof_document')->store('lead_docs', 'public');
        }
        // ... similar logic for other files ...
        if ($request->hasFile('co_itr_document')) {
             if ($lead->co_itr_document_path) Storage::disk('public')->delete($lead->co_itr_document_path);
            $data['co_itr_document_path'] = $request->file('co_itr_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('co_business_proof_document')) {
             if ($lead->co_business_proof_document_path) Storage::disk('public')->delete($lead->co_business_proof_document_path);
            $data['co_business_proof_document_path'] = $request->file('co_business_proof_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('guarantor_itr_document')) {
             if ($lead->guarantor_itr_document_path) Storage::disk('public')->delete($lead->guarantor_itr_document_path);
            $data['guarantor_itr_document_path'] = $request->file('guarantor_itr_document')->store('lead_docs', 'public');
        }
        if ($request->hasFile('guarantor_business_proof_document')) {
             if ($lead->guarantor_business_proof_document_path) Storage::disk('public')->delete($lead->guarantor_business_proof_document_path);
            $data['guarantor_business_proof_document_path'] = $request->file('guarantor_business_proof_document')->store('lead_docs', 'public');
        }

        // Recalculate Total Loan Amount
        $oldLoan = $request->input('old_loan_close_amount', 0);
        $inHand = $request->input('required_in_hand_amount', 0);
        $data['total_loan_amount'] = $oldLoan + $inHand;

        $lead->update($data);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:New,Prospect,Lead,Rejected,Not_Active',
            'remark' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'follow_up_time' => 'nullable',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_branch_id' => 'nullable|exists:bank_branches,id',
        ]);

        $data = [
            'status' => $request->status,
            'remark' => $request->remark,
        ];

        if ($request->status == 'Prospect' || $request->status == 'New') {
             if ($request->has('follow_up_date')) {
                 $data['follow_up_date'] = $request->follow_up_date;
                 $data['follow_up_time'] = $request->follow_up_time;
             }
        }

        if ($request->status == 'Lead') {
            $data['bank_id'] = $request->bank_id;
            $data['bank_branch_id'] = $request->bank_branch_id;
            if (!$lead->converted_at) {
                $data['converted_at'] = now();
            }
        }

        $lead->update($data);

        return redirect()->back()->with('success', 'Lead status updated successfully.');
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
