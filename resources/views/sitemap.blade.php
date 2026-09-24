{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach(['ar', 'en'] as $locale)
        @if(is_file(config('profile.source')) && is_file(config('profile.manifest')))
            <url><loc>{{ route('profile', ['locale' => $locale]) }}</loc></url>
        @endif
        @foreach(\App\Models\Page::published()->get() as $page)
            <url>
                <loc>{{ rtrim(config('app.url'), '/').'/'.$locale.($page->slug === 'home' ? '' : '/'.$page->slug) }}</loc>
                <lastmod>{{ $page->updated_at->toAtomString() }}</lastmod>
            </url>
        @endforeach
        @foreach(['sectors' => \App\Models\Sector::class, 'projects' => \App\Models\Project::class, 'news' => \App\Models\News::class] as $kind => $class)
            @if($kind !== 'sectors' && $class::published()->exists())
                <url><loc>{{ rtrim(config('app.url'), '/').'/'.$locale.'/'.$kind }}</loc></url>
            @endif
            @foreach($class::published()->get() as $item)
                <url>
                    <loc>{{ rtrim(config('app.url'), '/').'/'.$locale.'/'.$kind.'/'.$item->slug }}</loc>
                    <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
                </url>
            @endforeach
        @endforeach
    @endforeach
</urlset>
