<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Firm;
use App\Models\Bank;
use App\Models\BankBranch;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exports\EmployeeExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view employee', ['only' => ['index']]);
        $this->middleware('permission:create employee', ['only' => ['create','store']]);
        $this->middleware('permission:edit employee', ['only' => ['edit','update', 'updateStatus']]);
        $this->middleware('permission:delete employee', ['only' => ['destroy']]);
        $this->middleware('permission:export employee', ['only' => ['export']]);
    }

    public function index(Request $request)
    {
        // Ensure user has permission to view employees
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('view employee')) {
            abort(403, 'Unauthorized action.');
        }

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

        // Fetch Admins and Team Leads separately for dynamic filtering
        $admins = User::role('admin')->get();
        $teamLeads = User::role('Team Lead')->get();

        // Keep $managers for fallback or initial load if needed, though we'll use JS
        $managers = $admins->merge($teamLeads);

        $roles = Role::where('name', '!=', 'admin')->get();
        $permissions = Permission::all();
        return view('employees.create', compact('firms', 'banks', 'managers', 'admins', 'teamLeads', 'roles', 'permissions'));
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
            'education.*.degree' => 'nullable|string',
            'education.*.institution' => 'nullable|string',
            'education.*.passing_year' => 'nullable|string',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_branch_id' => 'nullable|exists:bank_branches,id',
            'account_holder_name' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'reporting_manager_id' => 'nullable|exists:users,id',
            'proof_id_number' => 'nullable|string|max:50',
            'proof_id_type' => 'nullable|string|max:50',
        ]);

        $data = $request->except('password', 'address_proof', 'education', 'role', 'permissions');
        $data['password'] = Hash::make($request->password);
        $data['employee_type'] = $request->role; // Storing role name in employee_type

        if ($request->hasFile('address_proof')) {
            $data['address_proof_path'] = $request->file('address_proof')->store('address_proofs', 'public');
        }

        // Generate Unique Employee ID: MM-YY-MM-DD-Serial
        $today = Carbon::today();
        $datePrefix = 'MM' . $today->format('y') . $today->format('m') . $today->format('d');

        // Count employees created today to get the next serial number
        $countToday = User::where('employee_id', 'like', $datePrefix . '%')->count();
        $serial = str_pad($countToday + 1, 2, '0', STR_PAD_LEFT);

        $data['employee_id'] = $datePrefix . $serial;

        $employee = User::create($data);
        $employee->assignRole($request->role);

        if ($request->has('permissions')) {
            $employee->syncPermissions($request->permissions);
        }

        if ($request->has('education')) {
            foreach ($request->education as $edu) {
                if (!empty($edu['degree']) || !empty($edu['institution']) || !empty($edu['passing_year'])) {
                    $employee->education()->create($edu);
                }
            }
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully. ID: ' . $data['employee_id']);
    }

    public function show(User $employee)
    {
        $employee->load('education', 'firm', 'reportingManager', 'bank', 'bankBranch');
        return view('employees.show-modal', compact('employee'))->render();
    }

    public function edit(User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('admin.employees.index')->with('error', 'Cannot edit admin user.');
        }
        $firms = Firm::all();
        $banks = Bank::all();

        // Fetch Admins and Team Leads separately
        $admins = User::role('admin')->where('id', '!=', $employee->id)->get();
        $teamLeads = User::role('Team Lead')->where('id', '!=', $employee->id)->get();
        $managers = $admins->merge($teamLeads);

        $roles = Role::where('name', '!=', 'admin')->get();
        $permissions = Permission::all();
        $employee->load('education', 'permissions', 'deniedPermissions'); // Load denied permissions
        return view('employees.edit', compact('employee', 'firms', 'banks', 'managers', 'admins', 'teamLeads', 'roles', 'permissions'));
    }

    public function update(Request $request, User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('admin.employees.index')->with('error', 'Cannot update admin user.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'role' => 'required|exists:roles,name',
            'contact_number' => 'required|string',
            'dob' => 'nullable|date',
            'firm_id' => 'required|exists:firms,id',
            'address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'education.*.degree' => 'nullable|string',
            'education.*.institution' => 'nullable|string',
            'education.*.passing_year' => 'nullable|string',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_branch_id' => 'nullable|exists:bank_branches,id',
            'account_holder_name' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'reporting_manager_id' => 'nullable|exists:users,id',
            'proof_id_number' => 'nullable|string|max:50',
            'proof_id_type' => 'nullable|string|max:50',
        ]);

        $data = $request->except('password', 'address_proof', 'education', 'role', 'permissions');
        $data['employee_type'] = $request->role; // Storing role name in employee_type

        if ($request->hasFile('address_proof')) {
            if ($employee->address_proof_path) {
                Storage::disk('public')->delete($employee->address_proof_path);
            }
            $data['address_proof_path'] = $request->file('address_proof')->store('address_proofs', 'public');
        }

        $employee->update($data);
        $employee->syncRoles([$request->role]);

        // Handle Permissions Logic
        $role = Role::where('name', $request->role)->first();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $selectedPermissions = $request->permissions ?? [];

        // 1. Direct Permissions: Permissions selected that are NOT in the role
        $directPermissions = array_diff($selectedPermissions, $rolePermissions);
        $employee->syncPermissions($directPermissions);

        // 2. Denied Permissions: Permissions in the role that were NOT selected
        $deniedPermissions = array_diff($rolePermissions, $selectedPermissions);

        // Sync denied permissions using the relationship
        $deniedPermissionIds = Permission::whereIn('name', $deniedPermissions)->pluck('id')->toArray();
        $employee->deniedPermissions()->sync($deniedPermissionIds);

        $employee->education()->delete();
        if ($request->has('education')) {
            foreach ($request->education as $edu) {
                if (!empty($edu['degree']) || !empty($edu['institution']) || !empty($edu['passing_year'])) {
                    $employee->education()->create($edu);
                }
            }
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('admin.employees.index')->with('error', 'Cannot delete admin user.');
        }
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function updateStatus(Request $request, User $employee)
    {
        if ($employee->hasRole('admin')) {
            return redirect()->route('admin.employees.index')->with('error', 'Cannot change status of admin user.');
        }
        $request->validate(['status' => 'required|in:Active,Blocked,Exited']);
        $employee->update(['status' => $request->status]);
        return redirect()->route('admin.employees.index')->with('success', 'Employee status updated successfully.');
    }

    public function export(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->can('export employee')) {
            abort(403, 'Unauthorized action.');
        }
        return (new EmployeeExport($request))->download();
    }

    public function getBranches(Bank $bank)
    {
        return response()->json($bank->branches);
    }
}
