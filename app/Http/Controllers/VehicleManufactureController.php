<?php

namespace App\Http\Controllers;

use App\Models\VehicleManufacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\VehicleManufactureExport;

class VehicleManufactureController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view vehicle manufacture', ['only' => ['index']]);
        $this->middleware('permission:create vehicle manufacture', ['only' => ['create','store']]);
        $this->middleware('permission:edit vehicle manufacture', ['only' => ['edit','update']]);
        $this->middleware('permission:delete vehicle manufacture', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = VehicleManufacture::with('vehicleNames');

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        $manufactures = $query->paginate(10);

        return view('vehicle_manufactures.index', compact('manufactures'));
    }

    public function create()
    {
        return view('vehicle_manufactures.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:vehicle_manufactures,name',
            'vehicle_names' => 'required|array',
            'vehicle_names.*' => 'required|string',
        ]);

        $manufacture = VehicleManufacture::create(['name' => $request->name]);

        foreach ($request->vehicle_names as $vehicleName) {
            $manufacture->vehicleNames()->create(['name' => $vehicleName]);
        }

        return redirect()->route('admin.vehicle-manufactures.index')->with('success', 'Vehicle Manufacture created successfully');
    }

    public function edit(VehicleManufacture $vehicleManufacture)
    {
        return view('vehicle_manufactures.edit', compact('vehicleManufacture'));
    }

    public function update(Request $request, VehicleManufacture $vehicleManufacture)
    {
        $request->validate([
            'name' => 'required|unique:vehicle_manufactures,name,' . $vehicleManufacture->id,
            'vehicle_names' => 'required|array',
            'vehicle_names.*' => 'required|string',
        ]);

        $vehicleManufacture->update(['name' => $request->name]);

        // Sync vehicle names: delete existing and recreate
        $vehicleManufacture->vehicleNames()->delete();
        foreach ($request->vehicle_names as $vehicleName) {
            $vehicleManufacture->vehicleNames()->create(['name' => $vehicleName]);
        }

        return redirect()->route('admin.vehicle-manufactures.index')->with('success', 'Vehicle Manufacture updated successfully');
    }

    public function destroy(VehicleManufacture $vehicleManufacture)
    {
        $vehicleManufacture->delete();
        return redirect()->route('admin.vehicle-manufactures.index')->with('success', 'Vehicle Manufacture deleted successfully');
    }

    public function toggleStatus(VehicleManufacture $vehicleManufacture)
    {
        $vehicleManufacture->update(['is_active' => !$vehicleManufacture->is_active]);
        return redirect()->route('admin.vehicle-manufactures.index')->with('success', 'Vehicle Manufacture status updated successfully');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return (new VehicleManufactureExport($request))->download();
    }

    public function getVehicleNames(VehicleManufacture $vehicleManufacture)
    {
        return response()->json($vehicleManufacture->vehicleNames);
    }
}
