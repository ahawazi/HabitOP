<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Apply the visitor's interface language.
 *
 * Priority: the signed-in user's saved preference, then the session (set by the
 * language switcher), then the application default.
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Locale::apply(Locale::resolve($request));

        return $next($request);
    }
}
