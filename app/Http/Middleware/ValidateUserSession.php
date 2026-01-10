<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $currentSessionId = session()->getId();

            // If user has a stored session ID and it doesn't match current session, logout
            if ($user->current_session_id && $user->current_session_id !== $currentSessionId) {
                auth()->logout();
                session()->flush();
                session()->regenerate();
                
                return response()->json([
                    'message' => 'Your account has been logged in from another location.'
                ], 401);
            }
        }

        return $next($request);
    }
}
