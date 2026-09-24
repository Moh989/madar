<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        return view('home', ['page' => Page::whereSlug('home')->published()->firstOrFail(), 'slides' => collect(Setting::valueFor('live_slides', []))->map(fn ($s) => new Slide($s)), 'projects' => Project::published()->orderBy('sort_order')->limit(3)->get(), 'news' => News::published()->orderByDesc('published_at')->limit(3)->get()]);
    }

    public function page(Request $r, string $locale, string $slug)
    {
        return view($slug === 'about' ? 'about' : 'page', ['page' => Page::published()->whereSlug($slug)->firstOrFail()]);
    }

    public function sectors()
    {
        return view('sectors', ['page' => Page::whereSlug('sectors')->published()->firstOrFail()]);
    }

    public function sector(string $locale, string $slug)
    {
        return view('detail', ['item' => Sector::published()->whereSlug($slug)->firstOrFail(), 'kind' => 'sectors']);
    }

    public function listing(Request $r, string $locale, string $kind)
    {
        $class = $kind === 'projects' ? Project::class : News::class;
        abort_unless($class::published()->exists(), 404);
        $q = $class::published()->orderBy('sort_order');
        if ($kind === 'projects' && $r->filled('sector')) {
            $q->where('sector_id', $r->integer('sector'));
        }

        return view('listing', ['items' => $q->paginate(9)->withQueryString(), 'kind' => $kind]);
    }

    public function detail(string $locale, string $kind, string $slug)
    {
        $class = $kind === 'projects' ? Project::class : News::class;

        return view('detail', ['item' => $class::published()->whereSlug($slug)->firstOrFail(), 'kind' => $kind]);
    }

    public function contact(Request $r, string $locale)
    {
        return view('contact', ['page' => Page::published()->whereSlug($r->routeIs('quote') ? 'quote' : 'contact')->firstOrFail(), 'quote' => $r->routeIs('quote')]);
    }

    public function media(Media $media)
    {
        return response()->file(storage_path('app/private/'.$media->path), ['Content-Type' => 'image/webp', 'Cache-Control' => 'public, max-age=86400']);
    }

    public function sitemap()
    {
        return response()->view('sitemap', [], 200)->header('Content-Type', 'application/xml');
    }
}
