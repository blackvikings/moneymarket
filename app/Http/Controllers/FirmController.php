<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use Illuminate\Http\Request;
use App\Exports\FirmExport;

class FirmController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view firm', ['only' => ['index']]);
        $this->middleware('permission:create firm', ['only' => ['create','store']]);
        $this->middleware('permission:edit firm', ['only' => ['edit','update']]);
        $this->middleware('permission:delete firm', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Firm::query();

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        $firms = $query->paginate(10);

        return view('firms.index', compact('firms'));
    }

    public function create()
    {
        return view('firms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:firms,name',
            'address' => 'required',
        ]);

        Firm::create($request->all());

        return redirect()->route('firms.index')->with('success', 'Firm created successfully');
    }

    public function edit(Firm $firm)
    {
        return view('firms.edit', compact('firm'));
    }

    public function update(Request $request, Firm $firm)
    {
        $request->validate([
            'name' => 'required|unique:firms,name,' . $firm->id,
            'address' => 'required',
        ]);

        $firm->update($request->all());

        return redirect()->route('firms.index')->with('success', 'Firm updated successfully');
    }

    public function destroy(Firm $firm)
    {
        $firm->delete();
        return redirect()->route('firms.index')->with('success', 'Firm deleted successfully');
    }

    public function toggleStatus(Firm $firm)
    {
        $firm->update(['is_active' => !$firm->is_active]);
        return redirect()->route('firms.index')->with('success', 'Firm status updated successfully');
    }

    public function export(Request $request)
    {
        return (new FirmExport($request))->download();
    }
}
