<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Firm;
use App\Models\Bank;
use App\Models\BankBranch;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Exports\EmployeeExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view employee', ['only' => ['index']]);
        $this->middleware('permission:create employee', ['only' => ['create','store']]);
        $this->middleware('permission:edit employee', ['only' => ['edit','update']]);
        $this->middleware('permission:delete employee', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        });

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $employees = $query->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $firms = Firm::all();
        $banks = Bank::all();
        $managers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Team Lead');
        })->get();
        $roles = Role::where('name', '!=', 'admin')->get();
        return view('employees.create', compact('firms', 'banks', 'managers', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'contact_number' => 'required|string',
            'dob' => 'nullable|date',
            'firm_id' => 'required|exists:firms,id',
            'current_address_line' => 'required|string',
            'permanent_address_line' => 'required|string',
            'address_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'education.*.degree' => 'required|string',
            'education.*.institution' => 'required|string',
            'education.*.passing_year' => 'required|string',
            'bank_id' => 'required_with:account_holder_name,ifsc_code|exists:banks,id',
            'bank_branch_id' => 'required_with:account_holder_name,ifsc_code|exists:bank_branches,id',
            'account_holder_name' => 'required_with:bank_id,ifsc_code|string',
            'ifsc_code' => 'required_with:bank_id,account_holder_name|string',
        ]);

        $data = $request->except('password', 'address_proof', 'education', 'role');
        $data['password'] = Hash::make($request->password);
        $data['employee_type'] = $request->role; // Storing role name in employee_type

        if ($request->hasFile('address_proof')) {
            $data['address_proof_path'] = $request->file('address_proof')->store('address_proofs', 'public');
        }

        $employee = User::create($data);
        $employee->assignRole($request->role);

        if ($request->has('education')) {
            foreach ($request->education as $edu) {
                $employee->education()->create($edu);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(User $employee)
    {
        $employee->load('education', 'firm', 'reportingManager', 'bank', 'bankBranch');
        return view('employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('employees.index')->with('error', 'Cannot edit admin user.');
        }
        $firms = Firm::all();
        $banks = Bank::all();
        $managers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Team Lead');
        })->where('id', '!=', $employee->id)->get();
        $roles = Role::where('name', '!=', 'admin')->get();
        $employee->load('education');
        return view('employees.edit', compact('employee', 'firms', 'banks', 'managers', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('employees.index')->with('error', 'Cannot update admin user.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'role' => 'required|exists:roles,name',
            'contact_number' => 'required|string',
            'dob' => 'nullable|date',
            'firm_id' => 'required|exists:firms,id',
            'address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'education.*.degree' => 'required|string',
            'education.*.institution' => 'required|string',
            'education.*.passing_year' => 'required|string',
        ]);

        $data = $request->except('password', 'address_proof', 'education', 'role');
        $data['employee_type'] = $request->role; // Storing role name in employee_type

        if ($request->hasFile('address_proof')) {
            if ($employee->address_proof_path) {
                Storage::disk('public')->delete($employee->address_proof_path);
            }
            $data['address_proof_path'] = $request->file('address_proof')->store('address_proofs', 'public');
        }

        $employee->update($data);
        $employee->syncRoles([$request->role]);

        $employee->education()->delete();
        if ($request->has('education')) {
            foreach ($request->education as $edu) {
                $employee->education()->create($edu);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('employees.index')->with('error', 'Cannot delete admin user.');
        }
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function updateStatus(Request $request, User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('employees.index')->with('error', 'Cannot change status of admin user.');
        }
        $request->validate(['status' => 'required|in:Active,Blocked,Exited']);
        $employee->update(['status' => $request->status]);
        return redirect()->route('employees.index')->with('success', 'Employee status updated successfully.');
    }

    public function export(Request $request)
    {
        return (new EmployeeExport($request))->download();
    }

    public function getBranches(Bank $bank)
    {
        return response()->json($bank->branches);
    }
}
