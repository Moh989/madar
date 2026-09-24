<x-layout :page="$page">
    <section class="container company-intro">
        <div>
            <span class="eyebrow">{{ __('site.about') }} <span aria-hidden="true">/</span> {{ __('site.about_kicker') }}</span>
            <h1>{{ $page->tr('title') }}</h1>
        </div>
        <div class="company-intro-copy">
            <p>{{ $page->tr('body') }}</p>
            @if($hasProfile)
                <a class="text-link" href="{{ route('profile') }}">{{ __('site.view_profile') }} <span>{{ __('site.arrow') }}</span></a>
            @endif
        </div>
    </section>
    <figure class="company-panorama">
        <img src="{{ asset('images/sea.webp') }}" width="1536" height="1024" alt="{{ __('site.about_image_alt') }}" fetchpriority="high">
        <figcaption class="container"><span>{{ __('site.about_since') }}</span><span>{{ __('site.tagline') }}</span></figcaption>
    </figure>
    <section class="container section company-story">
        <div><span class="eyebrow">{{ __('site.about_story_label') }}</span><h2>{{ __('site.about_story_title') }}</h2></div>
        <div class="company-prose">
            <p>{{ __('site.about_story_first') }}</p>
            <p>{{ __('site.about_story_second') }}</p>
            <div class="company-signature"><span class="company-signature-line" aria-hidden="true"></span>{{ __('site.about_identity_type') }}</div>
        </div>
    </section>
    <section class="company-direction">
        <div class="company-orbit" aria-hidden="true"></div>
        <div class="container section">
            <span class="eyebrow light">{{ __('site.about_approach') }}</span>
            <h2>{{ __('site.about_direction_title') }}</h2>
            <div class="company-direction-grid">
                @foreach(['vision', 'mission'] as $item)
                    <article><span class="company-section-number" aria-hidden="true">0{{ $loop->iteration }}</span><h3>{{ __('site.about_'.$item.'_title') }}</h3><p>{{ __('site.about_'.$item.'_text') }}</p></article>
                @endforeach
            </div>
        </div>
    </section>
    <section class="container section company-values">
        <span class="eyebrow">{{ __('site.about_values_label') }}</span>
        <h2>{{ __('site.about_values_title') }}</h2>
        <div class="company-values-grid">
            @foreach(range(1,4) as $number)
                <article><span class="company-value-index" aria-hidden="true">0{{ $number }}</span><h3>{{ __('site.about_value_'.$number.'_title') }}</h3><p>{{ __('site.about_value_'.$number.'_text') }}</p></article>
            @endforeach
        </div>
    </section>
    @if($sectors->isNotEmpty())
        <section class="company-portfolio">
            <div class="container section">
                <div class="company-portfolio-heading"><div><span class="eyebrow">{{ __('site.about_portfolio_label') }}</span><h2>{{ __('site.about_portfolio_title') }}</h2></div><p>{{ __('site.about_portfolio_intro') }}</p></div>
                @foreach([
                    ['image'=>'air', 'slugs'=>['air-freight','sea-freight','marine-services','import-export']],
                    ['image'=>'industry', 'slugs'=>['contracting','real-estate','industrial-investment']],
                    ['image'=>'agriculture', 'slugs'=>['supply','agriculture','packaging']],
                ] as $group)
                    @php($groupSectors=$sectors->whereIn('slug',$group['slugs']))
                    @if($groupSectors->isNotEmpty())
                        <article class="company-business-row">
                            <img src="{{ asset('images/'.$group['image'].'-small.webp') }}" width="800" height="450" loading="lazy" alt="{{ __('site.about_group_'.$loop->iteration.'_title') }}">
                            <div><span class="company-value-index" aria-hidden="true">0{{ $loop->iteration }}</span><h3>{{ __('site.about_group_'.$loop->iteration.'_title') }}</h3><p>{{ __('site.about_group_'.$loop->iteration.'_text') }}</p>
                                <div class="company-sector-links">@foreach($groupSectors as $sector)<a href="{{ route('sector',['slug'=>$sector->slug]) }}">{{ $sector->tr('title') }} <span aria-hidden="true">{{ __('site.arrow') }}</span></a>@endforeach</div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
    <section class="container section company-partnership">
        <div><span class="eyebrow">{{ __('site.about_partnership_label') }}</span><h2>{{ __('site.about_partnership_title') }}</h2><p>{{ __('site.about_partnership_text') }}</p><a class="text-link" href="{{ route('contact') }}">{{ __('site.contact') }} <span>{{ __('site.arrow') }}</span></a></div>
        <aside class="company-identity"><span class="eyebrow">{{ __('site.about_legal_label') }}</span><h3 lang="ar" dir="rtl">{{ __('site.legal_name') }}</h3>
            @php($brand=\App\Models\Setting::valueFor('brand',[]))
            @if($brand['english_approved']??false)<p lang="en" dir="ltr">{{ $brand['english_legal_name'] }}</p>@endif
            <div class="company-address">
                @if($contact['address'][app()->getLocale()]??null)<p>{{ $contact['address'][app()->getLocale()] }}</p>@endif
                @if($contact['email']??null)<a href="mailto:{{ $contact['email'] }}" dir="ltr">{{ $contact['email'] }}</a>@endif
            </div>
        </aside>
    </section>
    @if($hasProfile)
        <section class="container company-profile-callout">
            <div class="company-profile-cover" aria-hidden="true"><img src="{{ asset('images/profile-page-01.webp') }}" width="1800" height="2546" loading="lazy" alt=""></div>
            <div><span class="eyebrow">{{ __('site.profile') }}</span><h2>{{ __('site.about_profile_title') }}</h2><p>{{ __('site.about_profile_text') }}</p><a class="button button-navy" href="{{ route('profile') }}">{{ __('site.view_profile') }} <span>{{ __('site.arrow') }}</span></a></div>
        </section>
    @endif
    <x-cta/>
</x-layout>
