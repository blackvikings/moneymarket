<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\InsuranceLeadExport;

class InsuranceLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::whereNotNull('insurance_lead_stage');

        $leads = $query->latest()->paginate(10);

        return view('insurance_leads.index', compact('leads'));
    }

    public function create()
    {
        return view('insurance_leads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'insurance_address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except('insurance_address_proof');
        $data['created_by'] = Auth::id();
        $data['status'] = 'Lead';
        $data['rto_stage'] = 'RTO Done'; // Mark RTO as done
        $data['insurance_lead_stage'] = 'New'; // Start in Insurance Module
        $data['lead_type'] = 'Insurance';

        if ($request->hasFile('insurance_address_proof')) {
            $data['insurance_address_proof_path'] = $request->file('insurance_address_proof')->store('insurance_docs', 'public');
        }

        $lead = Lead::create($data);

        return redirect()->route(get_route_prefix() . 'insurance-leads.index')->with('success', 'New Insurance Lead added successfully.');
    }

    public function show(Lead $lead)
    {
        if (!$lead->insurance_lead_stage) {
            return redirect()->back()->with('error', 'This lead is not in the Insurance module.');
        }

        $lead->load(['insurance', 'vehicleName', 'manufacture']);
        $insurances = Insurance::where('is_active', true)->get();

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
        ]);

        $lead->update([
            'insurance_id' => $request->insurance_id,
            'insurance_start_date' => $request->insurance_start_date,
            'insurance_end_date' => $request->insurance_end_date,
            'insurance_type_of_insurance' => implode(', ', $request->insurance_type_of_insurance),
            'insurance_premium_amount' => $request->insurance_premium_amount,
            'insurance_idv_amount' => $request->insurance_idv_amount,
        ]);

        return redirect()->back()->with('success', 'Insurance details updated successfully.');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return (new InsuranceLeadExport($request))->download('insurance-leads.csv');
    }
}
