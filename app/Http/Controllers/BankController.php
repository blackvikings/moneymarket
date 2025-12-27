<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Exports\BankExport;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view bank', ['only' => ['index']]);
        $this->middleware('permission:create bank', ['only' => ['create','store']]);
        $this->middleware('permission:edit bank', ['only' => ['edit','update']]);
        $this->middleware('permission:delete bank', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Bank::with('branches');

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
            'branches' => 'nullable|array',
            'branches.*' => 'nullable|string',
        ]);

        $bank = Bank::create(['name' => $request->name]);

        if ($request->has('branches')) {
            foreach ($request->branches as $branchName) {
                if (!empty($branchName)) {
                    $bank->branches()->create(['name' => $branchName]);
                }
            }
        }

        return redirect()->route('banks.index')->with('success', 'Bank created successfully');
    }

    public function edit(Bank $bank)
    {
        $bank->load('branches');
        return view('banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|unique:banks,name,' . $bank->id,
            'branches' => 'nullable|array',
            'branches.*' => 'nullable|string',
        ]);

        $bank->update(['name' => $request->name]);

        // Sync branches: delete all and recreate (simplest approach for this requirement)
        $bank->branches()->delete();

        if ($request->has('branches')) {
            foreach ($request->branches as $branchName) {
                if (!empty($branchName)) {
                    $bank->branches()->create(['name' => $branchName]);
                }
            }
        }

        return redirect()->route('banks.index')->with('success', 'Bank updated successfully');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('banks.index')->with('success', 'Bank deleted successfully');
    }

    public function toggleStatus(Bank $bank)
    {
        $bank->update(['is_active' => !$bank->is_active]);
        return redirect()->route('banks.index')->with('success', 'Bank status updated successfully');
    }

    public function export(Request $request)
    {
        return (new BankExport($request))->download();
    }
}
