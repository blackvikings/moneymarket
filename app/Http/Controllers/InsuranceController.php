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
        $this->middleware('permission:edit insurance', ['only' => ['edit','update']]);
        $this->middleware('permission:delete insurance', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Insurance::with(['types.subTypes', 'state', 'city']);

        if ($request->has('company_name') && $request->company_name != '') {
            $query->where('company_name', 'like', '%' . $request->company_name . '%');
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
            'address_line' => 'required|string',
            'pincode' => 'required|string|max:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'types' => 'nullable|array',
            'types.*.name' => 'required_with:types|string',
            'types.*.sub_types' => 'nullable|array',
            'types.*.sub_types.*' => 'nullable|string',
        ]);

        $insurance = Insurance::create([
            'company_name' => $request->company_name,
            'agent_name' => $request->agent_name,
            'address_line' => $request->address_line,
            'pincode' => $request->pincode,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
        ]);

        if ($request->has('types') && is_array($request->types)) {
            foreach ($request->types as $typeData) {
                if (!empty($typeData['name'])) {
                    $type = $insurance->types()->create(['name' => $typeData['name']]);
                    if (isset($typeData['sub_types'])) {
                        foreach ($typeData['sub_types'] as $subTypeName) {
                            if (!empty($subTypeName)) {
                                $type->subTypes()->create(['name' => $subTypeName]);
                            }
                        }
                    }
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
            'address_line' => 'required|string',
            'pincode' => 'required|string|max:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'types' => 'nullable|array',
            'types.*.name' => 'required_with:types|string',
            'types.*.sub_types' => 'nullable|array',
            'types.*.sub_types.*' => 'nullable|string',
        ]);

        $insurance->update([
            'company_name' => $request->company_name,
            'agent_name' => $request->agent_name,
            'address_line' => $request->address_line,
            'pincode' => $request->pincode,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
        ]);

        // Delete existing types and recreate
        $insurance->types()->delete();

        if ($request->has('types') && is_array($request->types)) {
            foreach ($request->types as $typeData) {
                if (!empty($typeData['name'])) {
                    $type = $insurance->types()->create(['name' => $typeData['name']]);
                    if (isset($typeData['sub_types'])) {
                        foreach ($typeData['sub_types'] as $subTypeName) {
                            if (!empty($subTypeName)) {
                                $type->subTypes()->create(['name' => $subTypeName]);
                            }
                        }
                    }
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
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return (new InsuranceExport($request))->download();
    }
}
