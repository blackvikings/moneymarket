<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        });

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->paginate(10);

        return response()->json($employees);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(User $employee)
    {
        // Ensure we don't expose admin details through this endpoint
        if ($employee->hasRole('admin')) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $employee->load('education', 'firm', 'reportingManager', 'bank', 'bankBranch');
        return response()->json($employee);
    }
}
