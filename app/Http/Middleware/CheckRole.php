<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role, $guard = null)
    {
        $guard = $guard ?: config('auth.defaults.guard');
        $user = Auth::guard($guard)->user();

        if (!$user || !$user->hasRole($role)) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
