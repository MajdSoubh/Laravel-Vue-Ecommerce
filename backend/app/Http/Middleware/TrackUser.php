<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $response = $next($request);

        // Check if the header exists
        if ($request->headers->has('X-Unique-ID'))
        {
            // Add the custom header only if it doesn't exist
            $response->headers->set('X-Unique-ID', $request->header('X-Unique-ID'));
        }
        else
        {
            // Generate a new unique ID
            $uniqueId = uniqid();
            // Add the custom header only if it doesn't exist
            $response->headers->set('X-Unique-ID', $uniqueId);
        }

        return $response;
    }
}
