<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.employee-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('employee_id', 'password'), $request->filled('remember'))) {
            $user = Auth::user();
            if (!$user->hasRole('admin')) {
                $request->session()->regenerate();

                if ($user->is_first_login) {
                    return redirect()->route('employee.change-password');
                }

                return redirect()->intended('/home');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'employee_id' => 'Admins should login via the admin portal.',
                ]);
            }
        }

        return back()->withErrors([
            'employee_id' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('employee.login');
    }

    public function username()
    {
        return 'employee_id';
    }
}
