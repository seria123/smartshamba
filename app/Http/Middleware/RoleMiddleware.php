<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

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
