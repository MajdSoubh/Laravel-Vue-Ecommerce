<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard, ...$types): Response
    {

        if (auth($guard)->check())
        {
            auth()->shouldUse($guard);
            if (!in_array(auth()->user()->type, $types))
            {
                return response(['message' => 'You don\'t have permissions'], Response::HTTP_UNAUTHORIZED);
            }
            else
            {
                return $next($request);
            }
        }
        else
        {
            return response(['message' => 'You are not signed in'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
