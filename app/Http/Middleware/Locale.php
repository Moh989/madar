<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Support\Facades\URL;

class Locale
{
    public function handle($request, Closure $next)
    {
        $locale = $request->route('locale');
        abort_unless(in_array($locale, ['ar', 'en']), 404);
        app()->setLocale($locale);
        app('translator')->get('site', [], $locale);
        $copy = Setting::valueFor('copy', []);
        foreach (($copy[$locale] ?? []) as $key => $value) {
            if (is_string($value) && $value !== '') {
                app('translator')->addLines(['site.'.$key => $value], $locale);
            }
        }URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
