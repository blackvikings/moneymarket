<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Exports\CityExport;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view city', ['only' => ['index']]);
        $this->middleware('permission:create city', ['only' => ['create','store']]);
        $this->middleware('permission:edit city', ['only' => ['edit','update']]);
        $this->middleware('permission:delete city', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = City::query();

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        $cities = $query->paginate(10);

        return view('cities.index', compact('cities'));
    }

    public function create()
    {
        return view('cities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:cities,name',
        ]);

        City::create($request->all());

        return redirect()->route('cities.index')->with('success', 'City created successfully');
    }

    public function edit(City $city)
    {
        return view('cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|unique:cities,name,' . $city->id,
        ]);

        $city->update($request->all());

        return redirect()->route('cities.index')->with('success', 'City updated successfully');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('cities.index')->with('success', 'City deleted successfully');
    }

    public function toggleStatus(City $city)
    {
        $city->update(['is_active' => !$city->is_active]);
        return redirect()->route('cities.index')->with('success', 'City status updated successfully');
    }

    public function export(Request $request)
    {
        return (new CityExport($request))->download();
    }
}
