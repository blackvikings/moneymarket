<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\FirmExport;

class FirmController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view firm', ['only' => ['index', 'show']]);
        $this->middleware('permission:create firm', ['only' => ['create','store']]);
        $this->middleware('permission:edit firm', ['only' => ['edit','update', 'toggleStatus']]);
        $this->middleware('permission:delete firm', ['only' => ['destroy']]);
        $this->middleware('permission:export firm', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        $query = Firm::with(['state', 'city']);

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
            'address_line' => 'required|string',
            'pincode' => 'required|string|max:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
        ]);

        Firm::create($request->all());

        return redirect()->route('admin.firms.index')->with('success', 'Firm created successfully');
    }

    public function show(Firm $firm)
    {
        $firm->load(['state', 'city']);
        return view('firms.show-modal', compact('firm'))->render();
    }

    public function edit(Firm $firm)
    {
        return view('firms.edit', compact('firm'));
    }

    public function update(Request $request, Firm $firm)
    {
        $request->validate([
            'name' => 'required|unique:firms,name,' . $firm->id,
            'address_line' => 'required|string',
            'pincode' => 'required|string|max:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
        ]);

        $firm->update($request->all());

        return redirect()->route('admin.firms.index')->with('success', 'Firm updated successfully');
    }

    public function destroy(Firm $firm)
    {
        $firm->delete();
        return redirect()->route('admin.firms.index')->with('success', 'Firm deleted successfully');
    }

    public function toggleStatus(Firm $firm)
    {
        $firm->update(['is_active' => !$firm->is_active]);
        return redirect()->route('admin.firms.index')->with('success', 'Firm status updated successfully');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('export firm')) {
            abort(403, 'Unauthorized action.');
        }
        return (new FirmExport($request))->download();
    }
}
