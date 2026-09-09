<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
{
    if (Auth::check()) {
        $user = Auth::user();
        
        
        $role = is_object($user->role) ? $user->role->value : $user->role;

        if ($user && trim(strtolower($role)) === 'admin') {
            return $next($request);
        }
    }

    abort(403, 'Unauthorized action.');
}
}