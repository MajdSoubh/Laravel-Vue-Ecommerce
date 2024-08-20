<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Guest
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

            if (in_array(auth()->user()->type, $types))
            {
                return response(['message' => 'You have to be signed out first'], HttpStatusCode::BAD_REQUEST->value);
            }
        }
        return $next($request);
    }
}
