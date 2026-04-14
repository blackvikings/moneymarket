<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSessionTimeout
{
    private const TIMEOUT_SECONDS = 1800;

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('admin')) {
            return $next($request);
        }

        $lastActivity = (int) $request->session()->get('admin_last_activity_at', 0);
        $now = now()->timestamp;

        if ($lastActivity > 0 && ($now - $lastActivity) > self::TIMEOUT_SECONDS) {
            if ($user->active_admin_session_id === $request->session()->getId()) {
                $user->forceFill([
                    'active_admin_session_id' => null,
                ])->save();
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login.view')
                ->withErrors(['email' => 'Your admin session expired after 30 minutes of inactivity.']);
        }

        $request->session()->put('admin_last_activity_at', $now);

        return $next($request);
    }
}
