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
