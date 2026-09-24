<?php

namespace App\Http\Middleware;

use Closure;

class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $r = $next($request);
        $r->headers->set('X-Content-Type-Options', 'nosniff');
        $r->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $r->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $r->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        if ($request->is('admin*') || ! app()->isProduction()) {
            $r->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

return $r;
    }
}
