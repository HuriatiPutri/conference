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
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        // belum login
        if (!$user) {
            return redirect('/login');
        }

        // tidak punya role
        if (!$user->role) {
            abort(403, 'No role assigned');
        }

        // cek role
        if (!$user->hasRole($roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
