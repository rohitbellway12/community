<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberCommunityUrl
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
         * Only remember GET pages.
         *
         * We don't want to save:
         * - POST requests
         * - AJAX/API requests
         * - Login/Register pages
         * - Logout requests
         */
        if (
            $request->isMethod('GET') &&
            !$request->ajax() &&
            !$request->expectsJson() &&
            !$request->is('login') &&
            !$request->is('register') &&
            !$request->is('logout') &&
            $request->routeIs('community.*')
        ) {
            session([
                'community.intended_url' => $request->fullUrl(),
            ]);
        }

        return $next($request);
    }
}