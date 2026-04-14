<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('admin')) {
            return $next($request);
        }

        $activeSessionId = $user->active_admin_session_id;
        $currentSessionId = $request->session()->getId();

        if ($activeSessionId && $activeSessionId !== $currentSessionId) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login.view')
                ->withErrors(['email' => 'Your admin account is already active on another device.']);
        }

        return $next($request);
    }
}
