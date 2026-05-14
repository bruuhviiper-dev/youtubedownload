<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    
    public function handle(Request $request, Closure $next): Response
    {
        
        $locale = $request->segment(1);

        
        $supportedLocales = ['en', 'pt', 'es'];

        if (!in_array($locale, $supportedLocales)) {
            
            
            $locale = $request->getPreferredLanguage($supportedLocales) ?: config('app.locale');
        }

        
        App::setLocale($locale);

        
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
