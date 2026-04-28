<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\BankExport;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view bank', ['only' => ['index', 'show']]);
        $this->middleware('permission:create bank', ['only' => ['create','store', 'storeAjax', 'storeBranchAjax']]);
        $this->middleware('permission:edit bank', ['only' => ['edit','update', 'toggleStatus']]);
        $this->middleware('permission:delete bank', ['only' => ['destroy']]);
        $this->middleware('permission:export bank', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        $query = Bank::with(['branches.state', 'branches.city']);

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        $banks = $query->paginate(10);

        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:banks,name',
            'branches' => 'required|array',
            'branches.*.name' => 'required|string',
            'branches.*.address_line' => 'nullable|string',
            'branches.*.pincode' => 'nullable|string|max:10',
            'branches.*.state_id' => 'nullable|exists:states,id',
            'branches.*.city_id' => 'nullable|exists:cities,id',
        ]);

        $bank = Bank::create([
            'name' => $request->name,
        ]);

        if ($request->has('branches')) {
            foreach ($request->branches as $branchData) {
                $bank->branches()->create([
                    'name' => $branchData['name'],
                    'address_line' => $branchData['address_line'],
                    'pincode' => $branchData['pincode'],
                    'state_id' => $branchData['state_id'],
                    'city_id' => $branchData['city_id'],
                ]);
            }
        }

        return redirect()->route('admin.banks.index')->with('success', 'Bank created successfully');
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:banks,name',
            'branch_name' => 'nullable|string|max:255',
        ]);

        $bank = Bank::create([
            'name' => $request->name,
        ]);

        $branch = null;
        if ($request->filled('branch_name')) {
            $branch = $bank->branches()->create([
                'name' => $request->branch_name,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $branch ? 'Bank and branch created successfully' : 'Bank created successfully',
            'bank' => $bank,
            'branch' => $branch
        ]);
    }

    public function storeBranchAjax(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'name' => 'required|string|max:255',
            'address_line' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
        ]);

        $bank = Bank::findOrFail($request->bank_id);

        $branch = $bank->branches()->create([
            'name' => $request->name,
            'address_line' => $request->address_line,
            'pincode' => $request->pincode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Branch created successfully',
            'branch' => $branch,
        ]);
    }

    public function show(Bank $bank)
    {
        $bank->load(['branches.state', 'branches.city']);
        return view('banks.show-modal', compact('bank'))->render();
    }

    public function edit(Bank $bank)
    {
        $bank->load(['branches.state', 'branches.city']);
        return view('banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|unique:banks,name,' . $bank->id,
            'branches' => 'required|array',
            'branches.*.name' => 'required|string',
            'branches.*.address_line' => 'nullable|string',
            'branches.*.pincode' => 'nullable|string|max:10',
            'branches.*.state_id' => 'nullable|exists:states,id',
            'branches.*.city_id' => 'nullable|exists:cities,id',
        ]);

        $bank->update([
            'name' => $request->name,
        ]);

        // Sync branches: delete all and recreate
        $bank->branches()->delete();

        if ($request->has('branches')) {
            foreach ($request->branches as $branchData) {
                $bank->branches()->create([
                    'name' => $branchData['name'],
                    'address_line' => $branchData['address_line'],
                    'pincode' => $branchData['pincode'],
                    'state_id' => $branchData['state_id'],
                    'city_id' => $branchData['city_id'],
                ]);
            }
        }

        return redirect()->route('admin.banks.index')->with('success', 'Bank updated successfully');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('admin.banks.index')->with('success', 'Bank deleted successfully');
    }

    public function toggleStatus(Bank $bank)
    {
        // Check if user has edit permission before toggling status
        if (!Auth::user()->can('edit bank')) {
            abort(403, 'Unauthorized action.');
        }

        $bank->update(['is_active' => !$bank->is_active]);
        return redirect()->route('admin.banks.index')->with('success', 'Bank status updated successfully');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('export bank')) {
            abort(403, 'Unauthorized action.');
        }
        return (new BankExport($request))->download();
    }
}
