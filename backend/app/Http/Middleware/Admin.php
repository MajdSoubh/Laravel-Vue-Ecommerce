<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is admin
        if (auth()->user() && auth()->user()->is_admin)
        {
            return $next($request);
        }
        // if user not admin return an apropriate message
        else
        {
            return response()->json(['message' => 'You don\'t have permission to perform this action'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
