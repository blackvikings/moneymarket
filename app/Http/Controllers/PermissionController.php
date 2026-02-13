<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view permission', ['only' => ['index']]);
        $this->middleware('permission:create permission', ['only' => ['create','store']]);
        $this->middleware('permission:edit permission', ['only' => ['edit','update']]);
        $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->has('module') && $request->module != '') {
            $query->where('module', $request->module);
        }

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Increased pagination to keep groups together as much as possible
        $permissions = $query->orderBy('module')->orderBy('name')->paginate(50);

        // Get unique modules for filter dropdown
        $modules = Permission::select('module')->distinct()->whereNotNull('module')->pluck('module');

        return view('permissions.index', compact('permissions', 'modules'));
    }

    public function create()
    {
        // Get unique modules for dropdown
        $modules = Permission::select('module')->distinct()->whereNotNull('module')->pluck('module');
        return view('permissions.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'module' => 'required|string',
        ]);

        Permission::create([
            'name' => $request->name,
            'module' => $request->module,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully');
    }

    public function edit(Permission $permission)
    {
        // Get unique modules for dropdown
        $modules = Permission::select('module')->distinct()->whereNotNull('module')->pluck('module');
        return view('permissions.edit', compact('permission', 'modules'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$permission->id,
            'module' => 'required|string',
        ]);

        $permission->update([
            'name' => $request->name,
            'module' => $request->module,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully');
    }
}
