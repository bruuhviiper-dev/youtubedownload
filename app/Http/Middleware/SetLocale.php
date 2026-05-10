<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Get locale from segment (e.g., /en/...)
        $locale = $request->segment(1);

        // List of supported locales
        $supportedLocales = ['en', 'pt', 'es'];

        if (!in_array($locale, $supportedLocales)) {
            // 2. Fallback to browser language
            // getPreferredLanguage handles 'pt-BR' to 'pt' matching automatically if 'pt' is in the list
            $locale = $request->getPreferredLanguage($supportedLocales) ?: config('app.locale');
        }

        // Set the app locale
        App::setLocale($locale);

        // Share the locale for URL generation
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
