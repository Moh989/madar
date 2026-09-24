<?php

namespace App\Providers;

use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\Sector;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme(parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'http');
        RateLimiter::for('inquiries', fn ($r) => Limit::perMinute(4)->by($r->ip()));
        View::composer(['components.layout', 'components.nav', 'components.footer', 'home', 'sectors', 'contact', 'listing', 'page', 'about', 'detail', 'admin.preview'], function ($v) {
            $v->with('hasProfile', is_file(config('profile.source')) && is_file(config('profile.manifest')));
            $v->with('sectors', Sector::published()->orderBy('sort_order')->get());
            $v->with('contact', Setting::valueFor('contact', []));
            $v->with('nav', Setting::valueFor('navigation', []));
            $v->with('hasProjects', Project::published()->exists());
            $v->with('hasNews', News::published()->exists());
            $v->with('hasPrivacy', Page::whereSlug('privacy')->published()->exists());
        });
    }
}
