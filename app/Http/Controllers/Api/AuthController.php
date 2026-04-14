<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            // Prefer employee_id login for employees. Keep email as a fallback for older clients.
            'employee_id' => 'required_without:email|string',
            'email' => 'nullable|email',
            'password' => 'required|string',
        ]);

        $loginField = $request->filled('employee_id') ? 'employee_id' : 'email';
        $loginValue = $request->filled('employee_id') ? $request->employee_id : $request->email;

        $user = User::where($loginField, $loginValue)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                $loginField => ['The provided credentials do not match our records.'],
            ]);
        }

        // Prevent admins from using the employee API login
        if ($user->hasRole('admin')) {
            return response()->json(['message' => 'Admins cannot log in through this API.'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
