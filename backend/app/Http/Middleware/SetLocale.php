<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Default fallback locale
        $locale = config('app.fallback_locale');

        // Check for the 'lang' query parameter
        if ($request->has('lang'))
        {
            $locale = $request->query('lang');
        }

        // Check for the 'Accept-Language' header
        elseif ($request->header('Accept-Language'))
        {
            $locale = $request->header('Accept-Language');
        }

        // Validate and set the locale
        if (in_array($locale, config('app.available_locales')))
        {
            app()->setLocale($locale);
        }
        else
        {
            // Fallback to default locale if invalid
            app()->setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }
}
