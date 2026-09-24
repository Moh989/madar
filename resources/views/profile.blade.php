<x-layout :page="$page">
    <section class="container profile-heading">
        <div>
            <span class="eyebrow">{{ __('site.company') }} / {{ __('site.profile') }}</span>
            <h1>{{ __('site.profile_heading') }}</h1>
            <p>{{ __('site.profile_intro') }}</p>
        </div>
        <a class="profile-download" href="{{ route('profile.download', ['v' => filemtime(config('profile.source'))]) }}">
            <span class="download-icon" aria-hidden="true">↓</span>
            <span>{{ __('site.profile_download') }}<small dir="ltr">{{ __('site.profile_download_size') }}</small></span>
        </a>
    </section>
    <section class="magazine-shell" data-magazine data-current="{{ $currentPage }}"
        data-page-label="{{ __('site.profile_page') }}" data-of-label="{{ __('site.profile_of') }}"
        data-zoom-label="{{ __('site.profile_zoom') }}" data-fullscreen-label="{{ __('site.profile_fullscreen') }}"
        data-exit-fullscreen-label="{{ __('site.profile_exit_fullscreen') }}"
        data-error-label="{{ __('site.profile_error') }}"
        aria-label="{{ __('site.profile') }}">
        <div class="magazine-toolbar">
            <span class="magazine-title"><i data-lucide="book-open" aria-hidden="true"></i>{{ __('site.profile') }}</span>
            <div class="magazine-tools" data-enhanced-control hidden>
                <button type="button" data-reader-zoom><span aria-hidden="true">⊕</span>{{ __('site.profile_zoom') }}</button>
                <button type="button" data-toggle-thumbnails aria-controls="profile-thumbnails" aria-expanded="false"><span aria-hidden="true">▦</span>{{ __('site.profile_pages') }}</button>
                <button type="button" data-fullscreen hidden><span aria-hidden="true">⛶</span><span data-fullscreen-label>{{ __('site.profile_fullscreen') }}</span></button>
            </div>
        </div>
        <div class="magazine-stage" data-stage tabindex="0" aria-label="{{ __('site.profile_tip') }}">
            <button class="magazine-side-control magazine-side-previous" type="button" data-stage-previous data-enhanced-control hidden aria-label="{{ __('site.profile_previous') }}"><span aria-hidden="true">{{ app()->getLocale()==='ar'?'→':'←' }}</span></button>
            <button class="magazine-side-control magazine-side-next" type="button" data-stage-next data-enhanced-control hidden aria-label="{{ __('site.profile_next') }}"><span aria-hidden="true">{{ app()->getLocale()==='ar'?'←':'→' }}</span></button>
            <div class="magazine-book" data-book dir="rtl">
                <a class="magazine-page" data-page-number="{{ $currentPage }}" href="{{ asset($pages[$currentPage-1]['image']) }}" aria-label="{{ __('site.profile_zoom') }} — {{ __('site.profile_page') }} {{ $currentPage }}">
                    <img src="{{ asset($pages[$currentPage-1]['image']) }}" width="{{ $pages[$currentPage-1]['width'] }}" height="{{ $pages[$currentPage-1]['height'] }}" alt="{{ __('site.profile_page') }} {{ $currentPage }} — {{ __('site.profile') }}" fetchpriority="high">
                </a>
            </div>
            <p class="magazine-error" data-page-error role="alert" hidden></p>
        </div>
        <div class="magazine-navigation">
            <a class="page-turn-control" data-previous href="{{ route('profile',['page'=>max(1,$currentPage-1)]) }}" aria-label="{{ __('site.profile_previous') }}" @if($currentPage===1) aria-disabled="true" tabindex="-1" @endif><span aria-hidden="true">{{ app()->getLocale()==='ar'?'→':'←' }}</span></a>
            <div class="magazine-position" role="status" aria-live="polite" aria-atomic="true"><span class="sr-only">{{ __('site.profile_page') }}</span><bdi data-page-count>{{ str_pad($currentPage,2,'0',STR_PAD_LEFT) }}</bdi><span class="position-line" aria-hidden="true"></span><bdi>{{ count($pages) }}</bdi></div>
            <a class="page-turn-control" data-next href="{{ route('profile',['page'=>min(count($pages),$currentPage+1)]) }}" aria-label="{{ __('site.profile_next') }}" @if($currentPage===count($pages)) aria-disabled="true" tabindex="-1" @endif><span aria-hidden="true">{{ app()->getLocale()==='ar'?'←':'→' }}</span></a>
            <form class="magazine-jump" data-page-form action="{{ route('profile') }}" method="get">
                <label for="profile-page-number">{{ __('site.profile_page') }}</label>
                <input type="number" id="profile-page-number" name="page" min="1" max="{{ count($pages) }}" value="{{ $currentPage }}" inputmode="numeric" required>
                <button type="submit">{{ __('site.profile_go') }}</button>
            </form>
        </div>
        <div class="magazine-progress" aria-hidden="true"><span data-page-progress style="width:{{ $currentPage/count($pages)*100 }}%"></span></div>
        <nav class="magazine-thumbnails" id="profile-thumbnails" aria-label="{{ __('site.profile_pages') }}">
            @foreach($pages as $profilePage)
                <a data-thumbnail="{{ $profilePage['number'] }}" href="{{ route('profile',['page'=>$profilePage['number']]) }}" @if($profilePage['number']===$currentPage) aria-current="page" @endif aria-label="{{ __('site.profile_page') }} {{ $profilePage['number'] }}">
                    <img src="{{ asset($profilePage['thumbnail']) }}" width="180" height="255" loading="lazy" alt=""><bdi>{{ str_pad($profilePage['number'],2,'0',STR_PAD_LEFT) }}</bdi>
                </a>
            @endforeach
        </nav>
        <dialog class="profile-zoom-dialog" data-zoom-dialog aria-labelledby="profile-zoom-title">
            <div class="profile-zoom-toolbar">
                <h2 id="profile-zoom-title" data-zoom-title>{{ __('site.profile_zoom') }}</h2>
                <div>
                    <button type="button" data-zoom-out aria-label="{{ __('site.profile_zoom_out') }}">−</button>
                    <bdi data-zoom-level>100%</bdi>
                    <button type="button" data-zoom-in aria-label="{{ __('site.profile_zoom_in') }}">+</button>
                    <button type="button" data-zoom-close aria-label="{{ __('site.profile_close') }}">×</button>
                </div>
            </div>
            <div class="profile-zoom-scroll" data-zoom-scroll><img data-zoom-image alt=""></div>
        </dialog>
        <script type="application/json" data-profile-pages>{!! json_encode(collect($pages)->map(fn($entry)=>[...$entry,'image'=>asset($entry['image'])])->all(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
    </section>
    <div class="container profile-reading-notes">
        <p class="profile-hint" data-enhanced-control hidden>{{ __('site.profile_tip') }}</p>
        <details class="profile-text"><summary>{{ __('site.profile_text') }}</summary><div data-page-text><h2>{{ __('site.profile_page') }} {{ $currentPage }}</h2><p dir="auto">{{ $pages[$currentPage-1]['text'] }}</p></div></details>
    </div>
</x-layout>
