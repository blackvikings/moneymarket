<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankBranch;
use Illuminate\Http\Request;

class BankBranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view branch', ['only' => ['index']]);
        $this->middleware('permission:create branch', ['only' => ['create','store']]);
        $this->middleware('permission:edit branch', ['only' => ['edit','update']]);
        $this->middleware('permission:delete branch', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = BankBranch::with('bank');

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('bank_id') && $request->bank_id != '') {
            $query->where('bank_id', $request->bank_id);
        }

        $branches = $query->paginate(10);
        $banks = Bank::all();

        return view('bank_branches.index', compact('branches', 'banks'));
    }

    public function create()
    {
        $banks = Bank::all();
        return view('bank_branches.create', compact('banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'branches' => 'required|array',
            'branches.*' => 'required|string',
        ]);

        foreach ($request->branches as $branchName) {
            if (!empty($branchName)) {
                BankBranch::create([
                    'bank_id' => $request->bank_id,
                    'name' => $branchName,
                ]);
            }
        }

        return redirect()->route('bank-branches.index')->with('success', 'Branches created successfully');
    }

    public function edit(BankBranch $bankBranch)
    {
        $banks = Bank::all();
        return view('bank_branches.edit', compact('bankBranch', 'banks'));
    }

    public function update(Request $request, BankBranch $bankBranch)
    {
        $request->validate([
            'name' => 'required',
            'bank_id' => 'required|exists:banks,id',
        ]);

        $bankBranch->update($request->all());

        return redirect()->route('bank-branches.index')->with('success', 'Branch updated successfully');
    }

    public function destroy(BankBranch $bankBranch)
    {
        $bankBranch->delete();
        return redirect()->route('bank-branches.index')->with('success', 'Branch deleted successfully');
    }
}
