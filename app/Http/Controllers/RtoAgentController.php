<?php

namespace App\Http\Controllers;

use App\Models\RtoAgent;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\RtoAgentExport;

class RtoAgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view rto agent', ['only' => ['index', 'show']]);
        $this->middleware('permission:create rto agent', ['only' => ['create','store']]);
        $this->middleware('permission:edit rto agent', ['only' => ['edit','update']]);
        $this->middleware('permission:delete rto agent', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = RtoAgent::with('cities');

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        $agents = $query->paginate(10);

        return view('rto_agents.index', compact('agents'));
    }

    public function create()
    {
        $cities = City::where('is_active', true)->get();
        return view('rto_agents.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'number' => 'nullable|unique:rto_agents,number',
            'cities' => 'nullable|array',
            'cities.*' => 'nullable|string',
        ]);

        $agent = RtoAgent::create([
            'name' => $request->name,
            'number' => $request->number,
        ]);

        if ($request->has('cities') && is_array($request->cities)) {
            foreach ($request->cities as $cityName) {
                if (!empty($cityName)) {
                    $agent->cities()->create(['city_name' => $cityName]);
                }
            }
        }

        return redirect()->route('admin.rto-agents.index')->with('success', 'RTO Agent created successfully');
    }

    public function show(RtoAgent $rtoAgent)
    {
        $rtoAgent->load('cities');
        return view('rto_agents.show-modal', compact('rtoAgent'))->render();
    }

    public function edit(RtoAgent $rtoAgent)
    {
        $cities = City::where('is_active', true)->get();
        return view('rto_agents.edit', compact('rtoAgent', 'cities'));
    }

    public function update(Request $request, RtoAgent $rtoAgent)
    {
        $request->validate([
            'name' => 'required',
            'number' => 'nullable|unique:rto_agents,number,' . $rtoAgent->id,
            'cities' => 'nullable|array',
            'cities.*' => 'nullable|string',
        ]);

        $rtoAgent->update([
            'name' => $request->name,
            'number' => $request->number,
        ]);

        // Sync cities: delete existing and recreate
        $rtoAgent->cities()->delete();
        if ($request->has('cities') && is_array($request->cities)) {
            foreach ($request->cities as $cityName) {
                if (!empty($cityName)) {
                    $rtoAgent->cities()->create(['city_name' => $cityName]);
                }
            }
        }

        return redirect()->route('admin.rto-agents.index')->with('success', 'RTO Agent updated successfully');
    }

    public function destroy(RtoAgent $rtoAgent)
    {
        $rtoAgent->delete();
        return redirect()->route('admin.rto-agents.index')->with('success', 'RTO Agent deleted successfully');
    }

    public function toggleStatus(RtoAgent $rtoAgent)
    {
        $rtoAgent->update(['is_active' => !$rtoAgent->is_active]);
        return redirect()->route('admin.rto-agents.index')->with('success', 'RTO Agent status updated successfully');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('export rto agent')) {
            abort(403, 'Unauthorized action.');
        }
        return (new RtoAgentExport($request))->download();
    }
}
