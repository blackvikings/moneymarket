<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Insurance;
use App\Models\Firm;
use App\Models\VehicleManufacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\InsuranceLeadExport;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Carbon\Carbon;

class InsuranceLeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view insurance lead', ['only' => ['index', 'show', 'expiring']]);
        $this->middleware('permission:create insurance lead', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit insurance lead', ['only' => ['updateDetails', 'deleteDocument']]);
        $this->middleware('permission:export insurance lead', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        $query = Lead::whereNotNull('insurance_lead_stage');

        if ($request->has('status') && $request->status != '') {
            $query->where('insurance_lead_status', $request->status);
        }

        if ($request->has('premium_status') && $request->premium_status != '') {
            $query->where('premium_amount_collected', $request->premium_status);
        }

        $leads = $query->latest()->paginate(10);

        return view('insurance_leads.index', compact('leads'));
    }

    public function expiring(Request $request)
    {
        // List leads where insurance is expiring in the next 30 days (example logic)
        $query = Lead::whereNotNull('insurance_end_date')
                     ->whereBetween('insurance_end_date', [now(), now()->addDays(30)]);

        $leads = $query->orderBy('insurance_end_date', 'asc')->paginate(10);

        return view('insurance_leads.expiring', compact('leads'));
    }

    public function create()
    {
        $firms = Firm::where('is_active', true)->get();
        $manufactures = VehicleManufacture::where('is_active', true)->get();
        return view('insurance_leads.create', compact('firms', 'manufactures'));
    }

    public function store(Request $request)
    {
        // Validation logic should match what's in LeadController or customized here
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            // ... add other validations as needed
        ]);

        $data = $request->except(['insurance_address_proof', 'residence_type', 'aadhar_front', 'aadhar_back']);

        if ($request->input('lead_source') !== 'Any person') {
            $data['lead_source_person_name'] = null;
        }

        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['insurance_lead_stage'] = 'New';
        $data['lead_type'] = 'Insurance';
        $data['insurance_lead_status'] = 'In Progress'; // Default

        // Map vehicle details if provided
        if ($request->has('manufacture_id')) {
            $mfg = VehicleManufacture::find($request->manufacture_id);
            $data['mfg_name'] = $mfg ? $mfg->name : null;
        }

        $data['model_name'] = $request->model_name ?? $data['model_name'] ?? null;
        $data['variant'] = $request->variant ?? $data['variant'] ?? null;

        $lead = Lead::create($data);

        // Handle Media
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

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Lead created successfully.']);
        }

        return redirect()->route(get_route_prefix() . 'insurance-leads.index')->with('success', 'New Insurance Lead added successfully.');
    }

    public function show(Request $request, Lead $lead)
    {
        if (!$lead->insurance_lead_stage) {
            return redirect()->back()->with('error', 'This lead is not in the Insurance module.');
        }

        $lead->load(['insurance', 'vehicleName', 'manufacture', 'currentCity', 'currentState', 'permanentCity', 'permanentState', 'firm', 'media']);
        $insurances = Insurance::where('is_active', true)->get();

        if ($request->ajax()) {
            return view('insurance_leads.show-modal', compact('lead', 'insurances'))->render();
        }

        return view('insurance_leads.show', compact('lead', 'insurances'));
    }

    public function updateDetails(Request $request, Lead $lead)
    {
        $request->validate([
            'insurance_id' => 'required|exists:insurances,id',
            'insurance_start_date' => 'required|date',
            'insurance_end_date' => 'required|date',
            'premium_amount_collected' => 'required|in:Yes,No',
            'premium_payment_type' => 'required_if:premium_amount_collected,Yes|in:Cash,Cheque,Online,Bank Transfer',
            'premium_payment_amount' => 'required_if:premium_amount_collected,Yes|numeric',
            'premium_payment_date' => 'required_if:premium_amount_collected,Yes|date',
            'premium_cheque_number' => 'required_if:premium_payment_type,Cheque',
            'premium_cheque_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premium_upi_id' => 'required_if:premium_payment_type,Online',
            'premium_online_payment_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premium_account_holder_name' => 'required_if:premium_payment_type,Bank Transfer',
            'premium_account_number' => 'required_if:premium_payment_type,Bank Transfer',
            'premium_bank_name' => 'required_if:premium_payment_type,Bank Transfer',
            'premium_ifsc_code' => 'required_if:premium_payment_type,Bank Transfer',
            'premium_bank_transfer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->input('lead_source') !== 'Any person') {
            $data['lead_source_person_name'] = null;
        }

        if ($request->premium_amount_collected == 'No') {
            $data['premium_payment_type'] = null;
            $data['premium_payment_amount'] = null;
            $data['premium_payment_date'] = null;
        }

        if ($request->hasFile('premium_cheque_image')) {
            $data['premium_cheque_image_path'] = $request->file('premium_cheque_image')->store('premium_cheque_images', 'public');
        }
        if ($request->hasFile('premium_online_payment_image')) {
            $data['premium_online_payment_image_path'] = $request->file('premium_online_payment_image')->store('premium_online_payment_images', 'public');
        }
        if ($request->hasFile('premium_bank_transfer_image')) {
            $data['premium_bank_transfer_image_path'] = $request->file('premium_bank_transfer_image')->store('premium_bank_transfer_images', 'public');
        }

        // Handle Media Updates
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

        $lead->update($data);

        // Handle new address proofs from Dropzone (Removed in View, but keeping logic just in case)
        if ($request->hasFile('insurance_address_proof')) {
            foreach ($request->file('insurance_address_proof') as $file) {
                $lead->addMedia($file)->toMediaCollection('insurance_address_proof');
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Details updated successfully.']);
        }

        return redirect()->route(get_route_prefix() . 'insurance-leads.index')->with('success', 'Insurance details updated successfully.');
    }

    public function deleteDocument(Media $document)
    {
        $document->delete();
        return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('export insurance lead')) {
            abort(403, 'Unauthorized action.');
        }
        return (new InsuranceLeadExport($request))->download('insurance-leads.csv');
    }
}
