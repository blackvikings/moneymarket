<?php

namespace App\Http\Controllers;

use App\Models\VehicleManufacture;
use App\Models\VehicleName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\VehicleManufactureExport;

class VehicleManufactureController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view vehicle manufacture', ['only' => ['index', 'show']]);
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
        $manufactures = VehicleManufacture::all();
        return view('vehicle_manufactures.create', compact('manufactures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'vehicle_name' => 'required|string',
            'variants' => 'nullable|array',
            'variants.*' => 'nullable|string',
        ]);

        // Find or create manufacture
        $manufacture = VehicleManufacture::firstOrCreate(['name' => $request->name]);

        $variants = $request->input('variants', []);

        if (is_array($variants) && count($variants) > 0) {
            foreach ($variants as $variant) {
                if (!empty($variant)) {
                    $manufacture->vehicleNames()->create([
                        'name' => $request->vehicle_name,
                        'variant' => $variant
                    ]);
                }
            }
        } else {
            // Create at least one entry for the vehicle name even if no variants are provided
            $manufacture->vehicleNames()->create([
                'name' => $request->vehicle_name,
                'variant' => null
            ]);
        }

        return redirect()->route('admin.vehicle-manufactures.index')->with('success', 'Vehicle and variants saved successfully');
    }

    public function show(VehicleManufacture $vehicleManufacture)
    {
        $vehicleManufacture->load('vehicleNames');
        return view('vehicle_manufactures.show-modal', compact('vehicleManufacture'))->render();
    }

    public function edit(VehicleManufacture $vehicleManufacture)
    {
        $vehicleManufacture->load('vehicleNames');

        // Group variants by vehicle name for the view
        $groupedVehicles = $vehicleManufacture->vehicleNames->groupBy('name')->map(function($items) {
            return $items->pluck('variant')->filter()->toArray();
        });

        return view('vehicle_manufactures.edit', compact('vehicleManufacture', 'groupedVehicles'));
    }

    public function update(Request $request, VehicleManufacture $vehicleManufacture)
    {
        $request->validate([
            'name' => 'required|string|unique:vehicle_manufactures,name,' . $vehicleManufacture->id,
            'vehicles' => 'nullable|array',
            'vehicles.*.name' => 'required_with:vehicles|string',
            'vehicles.*.variants' => 'nullable|array',
            'vehicles.*.variants.*' => 'nullable|string',
        ]);

        $vehicleManufacture->update(['name' => $request->name]);

        // Sync vehicle names: delete existing and recreate
        $vehicleManufacture->vehicleNames()->delete();

        if ($request->has('vehicles') && is_array($request->vehicles)) {
            foreach ($request->vehicles as $vehicleData) {
                if (!empty($vehicleData['name'])) {
                    $variants = $vehicleData['variants'] ?? [];
                    if (is_array($variants) && count($variants) > 0) {
                        foreach ($variants as $variant) {
                            if (!empty($variant)) {
                                $vehicleManufacture->vehicleNames()->create([
                                    'name' => $vehicleData['name'],
                                    'variant' => $variant
                                ]);
                            }
                        }
                    } else {
                        $vehicleManufacture->vehicleNames()->create([
                            'name' => $vehicleData['name'],
                            'variant' => null
                        ]);
                    }
                }
            }
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
        // Return unique vehicle names for this manufacturer
        $names = $vehicleManufacture->vehicleNames()->select('name')->distinct()->get();
        return response()->json($names);
    }

    public function getVariantsByName(Request $request)
    {
        $vehicleName = $request->vehicleName;
        // Find variants for this specific vehicle name
        $variants = VehicleName::where('name', $vehicleName)->whereNotNull('variant')->select('variant')->get();
        return response()->json($variants);
    }
}
