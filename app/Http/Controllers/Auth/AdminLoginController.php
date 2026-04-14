<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $user = Auth::user();
            if ($user->hasRole('admin')) {
                $previousSessionId = $user->active_admin_session_id;

                $request->session()->regenerate();
                $currentSessionId = $request->session()->getId();

                if ($previousSessionId && $previousSessionId !== $currentSessionId) {
                    $request->session()->getHandler()->destroy($previousSessionId);
                }

                $user->forceFill([
                    'active_admin_session_id' => $currentSessionId,
                ])->save();
                $request->session()->put('admin_last_activity_at', now()->timestamp);

                return redirect()->intended('/home');
            } else {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'You are not authorized to access the admin panel.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->hasRole('admin') && $user->active_admin_session_id === $request->session()->getId()) {
            $user->forceFill([
                'active_admin_session_id' => null,
            ])->save();
        }

        $request->session()->forget('admin_last_activity_at');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/');
    }
}
