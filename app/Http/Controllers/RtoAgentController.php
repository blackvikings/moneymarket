<?php

namespace App\Http\Controllers;

use App\Models\RtoAgent;
use Illuminate\Http\Request;
use App\Exports\RtoAgentExport;

class RtoAgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view rto agent', ['only' => ['index']]);
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
        return view('rto_agents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'number' => 'required|unique:rto_agents,number',
            'cities' => 'required|array',
            'cities.*' => 'required|string',
        ]);

        $agent = RtoAgent::create([
            'name' => $request->name,
            'number' => $request->number,
        ]);

        foreach ($request->cities as $cityName) {
            $agent->cities()->create(['city_name' => $cityName]);
        }

        return redirect()->route('rto-agents.index')->with('success', 'RTO Agent created successfully');
    }

    public function edit(RtoAgent $rtoAgent)
    {
        return view('rto_agents.edit', compact('rtoAgent'));
    }

    public function update(Request $request, RtoAgent $rtoAgent)
    {
        $request->validate([
            'name' => 'required',
            'number' => 'required|unique:rto_agents,number,' . $rtoAgent->id,
            'cities' => 'required|array',
            'cities.*' => 'required|string',
        ]);

        $rtoAgent->update([
            'name' => $request->name,
            'number' => $request->number,
        ]);

        // Sync cities: delete existing and recreate
        $rtoAgent->cities()->delete();
        foreach ($request->cities as $cityName) {
            $rtoAgent->cities()->create(['city_name' => $cityName]);
        }

        return redirect()->route('rto-agents.index')->with('success', 'RTO Agent updated successfully');
    }

    public function destroy(RtoAgent $rtoAgent)
    {
        $rtoAgent->delete();
        return redirect()->route('rto-agents.index')->with('success', 'RTO Agent deleted successfully');
    }

    public function toggleStatus(RtoAgent $rtoAgent)
    {
        $rtoAgent->update(['is_active' => !$rtoAgent->is_active]);
        return redirect()->route('rto-agents.index')->with('success', 'RTO Agent status updated successfully');
    }

    public function export(Request $request)
    {
        return (new RtoAgentExport($request))->download();
    }
}
