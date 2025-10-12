<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use BackedEnum;
use UnitEnum;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        // Allowed roles from route: 'role:admin' or 'role:admin,manager'
        $allowedRoles = collect($roles)
            ->flatMap(fn($r) => explode(',', $r))
            ->map(fn($r) => strtolower(trim($r)))
            ->filter()
            ->values();

        // Normalize the user's role to a lowercase string
        $userRole = $this->normalizeRole(Auth::user()->role);

        if ($allowedRoles->isNotEmpty() && ! $allowedRoles->contains($userRole)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            abort(403, 'Unauthorized. Tui K vai?');
        }

        return $next($request);
    }

    private function normalizeRole(mixed $role): string
    {
        if ($role instanceof BackedEnum) {
            return strtolower((string) $role->value);
        }
        if ($role instanceof UnitEnum) {
            return strtolower($role->name);
        }
        return strtolower((string) $role);
    }
}
