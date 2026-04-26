<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HybridAuth
{
    /**
     * Handle an incoming request.
     *
     * Accepts authentication via:
     * - Session (web guard) - traditional Blade app
     * - Sanctum token (sanctum guard) - SPA/mobile apps with Bearer token
     *
     * Sets the appropriate default guard so downstream code can use Auth::user()
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check session-based authentication first (web guard)
        if (Auth::guard('web')->check()) {
            // Ensure web guard is used for Auth::user() calls
            Auth::shouldUse('web');

            return $next($request);
        }

        // Check for Bearer token in Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);

            // Authenticate using Sanctum guard
            if (Auth::guard('sanctum')->check()) {
                // Tell Laravel to use sanctum guard for this request
                Auth::shouldUse('sanctum');

                return $next($request);
            }
        }

        // Check Sanctum via cookie-based SPA authentication (Sanctum's SPA mode)
        if (Auth::guard('sanctum')->check()) {
            Auth::shouldUse('sanctum');

            return $next($request);
        }

        // Neither authentication method succeeded
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return redirect()->route('login');
    }
}
