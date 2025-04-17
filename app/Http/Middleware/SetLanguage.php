<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;

use Closure;
use Illuminate\Http\Request;

class SetLanguage
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
        $lang = $request->getPreferredLanguage(['en', 'ko']) ?? 'en';
        // if (session()->has('locale')) {
        //     App::setLocale(session()->get('locale'));
        // }

        App::setLocale($lang);
        return $next($request);
    }
}
