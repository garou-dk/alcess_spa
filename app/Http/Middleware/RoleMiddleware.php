<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $hasAccess = explode('-', $roles);
        abort_unless(
            in_array(Auth::guard('web')->user()->role->role_name, $hasAccess),
            Response::HTTP_FORBIDDEN,
            'Access denied!'
        );

        return $next($request);
    }
}
