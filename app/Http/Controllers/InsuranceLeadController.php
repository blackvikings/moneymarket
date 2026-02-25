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

class InsuranceLeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view insurance lead', ['only' => ['index', 'show']]);
        $this->middleware('permission:create insurance lead', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit insurance lead', ['only' => ['updateDetails', 'deleteDocument']]);
        $this->middleware('permission:export insurance lead', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        $query = Lead::whereNotNull('insurance_lead_stage');

        $leads = $query->latest()->paginate(10);

        return view('insurance_leads.index', compact('leads'));
    }

    public function create()
    {
        $firms = Firm::where('is_active', true)->get();
        $manufactures = VehicleManufacture::where('is_active', true)->get();
        return view('insurance_leads.create', compact('firms', 'manufactures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'firm_id' => 'nullable|exists:firms,id',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
            'insurance_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_number' => 'nullable|string|max:20',
            'current_address_line' => 'nullable|string',
            'current_address_pincode' => 'nullable|string',
            'current_address_state' => 'nullable|exists:states,id',
            'current_address_city' => 'nullable|exists:cities,id',
            'permanent_address_line' => 'nullable|string',
            'permanent_address_pincode' => 'nullable|string',
            'permanent_address_state' => 'nullable|exists:states,id',
            'permanent_address_city' => 'nullable|exists:cities,id',
            'insurance_address_proof_type' => 'nullable|string',
            'manufacture_id' => 'nullable|exists:vehicle_manufactures,id',
            'vehicle_name_id' => 'nullable|exists:vehicle_names,id',
        ]);

        $data = $request->except(['insurance_address_proof', 'residence_type']);

        // Unset lead_source_person_name if lead_source is not 'Any Person'
        if ($request->input('lead_source') !== 'Any Person') {
            $data['lead_source_person_name'] = null;
        }

        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['rto_stage'] = 'RTO Done';
        $data['insurance_lead_stage'] = 'New';
        $data['lead_type'] = 'Insurance';

        // Map vehicle details if provided
        if ($request->has('manufacture_id')) {
            $mfg = VehicleManufacture::find($request->manufacture_id);
            $data['mfg_name'] = $mfg ? $mfg->name : null;
        }

        $data['model_name'] = $request->model_name ?? $data['model_name'] ?? null;
        $data['variant'] = $request->variant ?? $data['variant'] ?? null;

        $lead = Lead::create($data);

        if ($request->hasFile('insurance_address_proof')) {
            foreach ($request->file('insurance_address_proof') as $file) {
                $lead->addMedia($file)
                     ->usingName($request->insurance_address_proof_type ?? 'Address Proof')
                     ->toMediaCollection('insurance_address_proof');
            }
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

        // Removed 'documents' from load, added 'media'
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
            'insurance_type_of_insurance' => 'required|array',
            'insurance_premium_amount' => 'required|numeric',
            'insurance_idv_amount' => 'required|numeric',
            'pan_card_number' => 'nullable|string|max:20',
            'pan_card_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'rc_number' => 'nullable|string|max:100',
            'rc_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'insurance_address_proof.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'lead_source' => 'nullable|string|max:255',
            'lead_source_person_name' => 'nullable|string|max:255',
        ]);

        $data = [
            'insurance_id' => $request->insurance_id,
            'insurance_start_date' => $request->insurance_start_date,
            'insurance_end_date' => $request->insurance_end_date,
            'insurance_type_of_insurance' => implode(', ', $request->insurance_type_of_insurance),
            'insurance_premium_amount' => $request->insurance_premium_amount,
            'insurance_idv_amount' => $request->insurance_idv_amount,
            'pan_card_number' => $request->pan_card_number,
            'rc_number' => $request->rc_number,
            'lead_source' => $request->lead_source,
            'lead_source_person_name' => $request->lead_source_person_name,
        ];

        // Unset lead_source_person_name if lead_source is not 'Any Person'
        if ($request->input('lead_source') !== 'Any Person') {
            $data['lead_source_person_name'] = null;
        }

        if ($request->hasFile('pan_card_document')) {
            $lead->addMediaFromRequest('pan_card_document')->toMediaCollection('insurance_pan_card');
        }

        if ($request->hasFile('rc_document')) {
            $lead->addMediaFromRequest('rc_document')->toMediaCollection('insurance_rc');
        }

        $lead->update($data);

        // Handle new address proofs from Dropzone
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
