<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            // Not logged in
            return redirect()->route('login');
        }

        // if no roles passed, allow any authenticated user
        if (empty($roles)) {
            return $next($request);
        }

        // Support enum or string
        $userRole = is_object($user->role) && method_exists($user->role, 'value') ? $user->role->value : $user->role;

        if (! in_array($userRole, $roles, true)) {
            // You can redirect to a “no access” page instead of aborting
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
