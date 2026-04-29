<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        // If no user is authenticated, redirect to login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // If no role is specified, allow access to any authenticated user
        if ($role === null) {
            return $next($request);
        }

        // For the 'admin' role requirement, allow either admin or manager
        if ($role === 'admin') {
            if (! $user->isAdmin() && ! $user->isManager()) {
                return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
            }
        } elseif ($role === 'manager') {
            // Only allow exact manager role
            if (! $user->isManager()) {
                return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
            }
        } elseif ($role === 'user') {
            // Only allow exact user role
            if (! $user->isUser()) {
                return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
}
