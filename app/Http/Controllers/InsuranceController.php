<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use App\Models\InsuranceType;
use App\Models\InsuranceSubType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\InsuranceExport;

class InsuranceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view insurance', ['only' => ['index', 'show']]);
        $this->middleware('permission:create insurance', ['only' => ['create','store']]);
        $this->middleware('permission:edit insurance', ['only' => ['edit','update', 'toggleStatus']]);
        $this->middleware('permission:delete insurance', ['only' => ['destroy']]);
        $this->middleware('permission:export insurance', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        $query = Insurance::with(['types.subTypes', 'state', 'city']);

        if ($request->has('company_name') && $request->company_name != '') {
            $query->where('company_name', 'like', '%' . $request->company_name . '%');
        }

        if ($request->has('vehicle_type') && $request->vehicle_type != '') {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        $insurances = $query->paginate(10);

        return view('insurances.index', compact('insurances'));
    }

    public function create()
    {
        return view('insurances.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|unique:insurances,company_name',
            'agent_name' => 'required',
            'vehicle_type' => 'required|in:2 Wheeler,3 Wheeler,4 Wheeler,Commercial',
            'types' => 'nullable|array',
            'types.*.name' => 'required_with:types|string',
        ]);

        $insurance = Insurance::create([
            'company_name' => $request->company_name,
            'agent_name' => $request->agent_name,
            'vehicle_type' => $request->vehicle_type,
        ]);

        if ($request->has('types') && is_array($request->types)) {
            foreach ($request->types as $typeData) {
                if (!empty($typeData['name'])) {
                    $insurance->types()->create(['name' => $typeData['name']]);
                }
            }
        }

        return redirect()->route('admin.insurances.index')->with('success', 'Insurance created successfully');
    }

    public function show(Insurance $insurance)
    {
        $insurance->load(['types.subTypes', 'state', 'city']);
        return view('insurances.show-modal', compact('insurance'))->render();
    }

    public function edit(Insurance $insurance)
    {
        $insurance->load('types.subTypes');
        return view('insurances.edit', compact('insurance'));
    }

    public function update(Request $request, Insurance $insurance)
    {
        $request->validate([
            'company_name' => 'required|unique:insurances,company_name,' . $insurance->id,
            'agent_name' => 'required',
            'vehicle_type' => 'required|in:2 Wheeler,3 Wheeler,4 Wheeler,Commercial',
            'types' => 'nullable|array',
            'types.*.name' => 'required_with:types|string',
        ]);

        $insurance->update([
            'company_name' => $request->company_name,
            'agent_name' => $request->agent_name,
            'vehicle_type' => $request->vehicle_.type,
        ]);

        // Delete existing types and recreate
        $insurance->types()->delete();

        if ($request->has('types') && is_array($request->types)) {
            foreach ($request->types as $typeData) {
                if (!empty($typeData['name'])) {
                    $insurance->types()->create(['name' => $typeData['name']]);
                }
            }
        }

        return redirect()->route('admin.insurances.index')->with('success', 'Insurance updated successfully');
    }

    public function destroy(Insurance $insurance)
    {
        $insurance->delete();
        return redirect()->route('admin.insurances.index')->with('success', 'Insurance deleted successfully');
    }

    public function toggleStatus(Insurance $insurance)
    {
        $insurance->update(['is_active' => !$insurance->is_active]);
        return redirect()->route('admin.insurances.index')->with('success', 'Insurance status updated successfully');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('export insurance')) {
            abort(403, 'Unauthorized action.');
        }
        return (new InsuranceExport($request))->download();
    }
}
