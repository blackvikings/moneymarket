<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['create','store']]);
        $this->middleware('permission:edit role', ['only' => ['edit','update']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        $permissions = Permission::whereIn('id', $request->permissions)->get();

        // Automatically add 'show master option' if any master view permission is selected
        $masterPermissions = ['view bank', 'view firm', 'view rto agent', 'view insurance', 'view vehicle manufacture'];
        $hasMasterPermission = $permissions->pluck('name')->intersect($masterPermissions)->isNotEmpty();

        if ($hasMasterPermission) {
            $showMasterOption = Permission::where('name', 'show master option')->first();
            if ($showMasterOption && !$permissions->contains($showMasterOption)) {
                $permissions->push($showMasterOption);
            }
        }

        // Automatically add 'show employee option' if any employee view permission is selected
        $employeePermissions = ['view employee', 'view role', 'view permission'];
        $hasEmployeePermission = $permissions->pluck('name')->intersect($employeePermissions)->isNotEmpty();

        if ($hasEmployeePermission) {
            $showEmployeeOption = Permission::where('name', 'show employee option')->first();
            if ($showEmployeeOption && !$permissions->contains($showEmployeeOption)) {
                $permissions->push($showEmployeeOption);
            }
        }

        // Automatically add 'show lms option' if any lms view permission is selected
        $lmsPermissions = ['view lead', 'create lead', 'edit lead', 'delete lead', 'export lead', 'import lead', 'bulk assign lead'];
        $hasLmsPermission = $permissions->pluck('name')->intersect($lmsPermissions)->isNotEmpty();

        if ($hasLmsPermission) {
            $showLmsOption = Permission::where('name', 'show lms option')->first();
            if ($showLmsOption && !$permissions->contains($showLmsOption)) {
                $permissions->push($showLmsOption);
            }
        }

        // Automatically add 'show crm option' if any crm view permission is selected
        $crmPermissions = ['view crm', 'edit crm', 'export crm', 'view rto', 'create rto', 'edit rto', 'export rto', 'view insurance lead', 'create insurance lead', 'edit insurance lead', 'export insurance lead'];
        $hasCrmPermission = $permissions->pluck('name')->intersect($crmPermissions)->isNotEmpty();

        if ($hasCrmPermission) {
            $showCrmOption = Permission::where('name', 'show crm option')->first();
            if ($showCrmOption && !$permissions->contains($showCrmOption)) {
                $permissions->push($showCrmOption);
            }
        }

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot edit admin role.');
        }
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot update admin role.');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $request->name]);
        $permissions = Permission::whereIn('id', $request->permissions)->get();

        // Automatically add 'show master option' if any master view permission is selected
        $masterPermissions = ['view bank', 'view firm', 'view rto agent', 'view insurance', 'view vehicle manufacture'];
        $hasMasterPermission = $permissions->pluck('name')->intersect($masterPermissions)->isNotEmpty();

        if ($hasMasterPermission) {
            $showMasterOption = Permission::where('name', 'show master option')->first();
            if ($showMasterOption && !$permissions->contains($showMasterOption)) {
                $permissions->push($showMasterOption);
            }
        }

        // Automatically add 'show employee option' if any employee view permission is selected
        $employeePermissions = ['view employee', 'view role', 'view permission'];
        $hasEmployeePermission = $permissions->pluck('name')->intersect($employeePermissions)->isNotEmpty();

        if ($hasEmployeePermission) {
            $showEmployeeOption = Permission::where('name', 'show employee option')->first();
            if ($showEmployeeOption && !$permissions->contains($showEmployeeOption)) {
                $permissions->push($showEmployeeOption);
            }
        }

        // Automatically add 'show lms option' if any lms view permission is selected
        $lmsPermissions = ['view lead', 'create lead', 'edit lead', 'delete lead', 'export lead', 'import lead', 'bulk assign lead'];
        $hasLmsPermission = $permissions->pluck('name')->intersect($lmsPermissions)->isNotEmpty();

        if ($hasLmsPermission) {
            $showLmsOption = Permission::where('name', 'show lms option')->first();
            if ($showLmsOption && !$permissions->contains($showLmsOption)) {
                $permissions->push($showLmsOption);
            }
        }

        // Automatically add 'show crm option' if any crm view permission is selected
        $crmPermissions = ['view crm', 'edit crm', 'export crm', 'view rto', 'create rto', 'edit rto', 'export rto', 'view insurance lead', 'create insurance lead', 'edit insurance lead', 'export insurance lead'];
        $hasCrmPermission = $permissions->pluck('name')->intersect($crmPermissions)->isNotEmpty();

        if ($hasCrmPermission) {
            $showCrmOption = Permission::where('name', 'show crm option')->first();
            if ($showCrmOption && !$permissions->contains($showCrmOption)) {
                $permissions->push($showCrmOption);
            }
        }

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete admin role.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
    }
}
