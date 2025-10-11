<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $role = is_object($request->user()->role) && method_exists($request->user()->role, 'value')
                ? $request->user()->role->value
                : $request->user()->role;

            return match ($role) {
                'admin'   => redirect()->route('dashboard'),
                'manager' => redirect()->route('dashboard'),
                'user'    => redirect()->route('home'),
                default   => redirect()->route('home'),
            };
        }

        return $next($request);
    }
}
