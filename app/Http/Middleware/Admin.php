<?php

namespace App\Http\Middleware;

use Closure;

class Admin
{
    public function handle($request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }abort_unless(auth()->user()->is_admin, 403);

        return $next($request);
    }
}
