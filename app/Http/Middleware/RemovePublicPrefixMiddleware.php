<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class RemovePublicPrefixMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Str::startsWith($request->getRequestUri(), '/public')) {
            $newUri = Str::replaceFirst('/public', '', $request->getRequestUri());
            return redirect($newUri, 301);
        }

        return $next($request);
    }
}
