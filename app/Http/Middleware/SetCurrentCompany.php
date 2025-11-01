<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCurrentCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // THIS LINE IS 100% REQUIRED
            app(\Spatie\Permission\PermissionRegistrar::class)
                ->setPermissionsTeamId(auth()->user()->company_id);
        }
        return $next($request);
    }
}
