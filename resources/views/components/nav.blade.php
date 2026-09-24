@props(['home'=>false,'switch'=>'/en'])
@php
$links=collect($nav)->sortBy('order')->filter(fn($v)=>$v['visible']??true);
$hrefs=['home'=>route('home'),'about'=>route('page',['slug'=>'about']),'sectors'=>route('sectors'),'projects'=>route('listing',['kind'=>'projects']),'news'=>route('listing',['kind'=>'news']),'contact'=>route('contact'),'profile'=>route('profile')];
@endphp
<header class="site-header {{ $home?'over-hero':'' }}" data-header>
<div class="nav-shell container">
<a class="brand" href="{{ route('home') }}" aria-label="{{ __('site.home') }}"><img src="{{ asset('images/company-mark.png') }}" width="65" height="60" alt=""><span><strong lang="ar">مدار العالم</strong><small>{{ app()->getLocale()==='ar'?'نصل بالأعمال إلى آفاق أوسع':'BUSINESS. CONNECTED.' }}</small></span></a>
<nav class="desktop-nav" aria-label="{{ __('site.quick_links') }}">
@foreach($links as $key=>$value)
@continue(($key==='projects'&&!$hasProjects)||($key==='news'&&!$hasNews)||($key==='profile'&&!$hasProfile)||!isset($hrefs[$key]))
@if($key==='sectors')<details class="sector-dropdown"><summary>{{ __('site.sectors') }} <span aria-hidden="true">⌄</span></summary><div class="dropdown-panel"><a class="dropdown-all" href="{{ route('sectors') }}">{{ __('site.all_sectors') }} <span>{{ __('site.arrow') }}</span></a>@foreach($sectors as $sector)<a href="{{ route('sector',['slug'=>$sector->slug]) }}">{{ $sector->tr('title') }}</a>@endforeach</div></details>
@else<a href="{{ $hrefs[$key] }}" @class(['active'=>url()->current()===$hrefs[$key]])>{{ __('site.'.$key) }}</a>@endif
@endforeach
</nav>
<div class="nav-actions"><a class="language" href="{{ $switch }}" lang="{{ app()->getLocale()==='ar'?'en':'ar' }}"><i data-lucide="globe-2"></i>{{ app()->getLocale()==='ar'?'EN':'عربي' }}</a><a class="button button-small nav-quote" href="{{ route('quote') }}">{{ __('site.quote') }} <span>{{ __('site.arrow') }}</span></a><button class="icon-button mobile-toggle" data-menu-open aria-controls="mobile-menu" aria-expanded="false" aria-label="{{ __('site.menu') }}"><i data-lucide="menu"></i><span class="sr-only">{{ __('site.menu') }}</span></button></div>
</div></header>
<dialog class="mobile-menu" id="mobile-menu"><div class="mobile-menu-top"><strong>مدار العالم</strong><button class="icon-button" data-menu-close aria-label="{{ __('site.close') }}">×</button></div><nav>@foreach($links as $key=>$value)@continue(($key==='projects'&&!$hasProjects)||($key==='news'&&!$hasNews)||($key==='profile'&&!$hasProfile)||!isset($hrefs[$key]))<a href="{{ $hrefs[$key] }}">{{ __('site.'.$key) }} <span>{{ __('site.arrow') }}</span></a>@endforeach</nav><a class="button" href="{{ route('quote') }}">{{ __('site.quote') }}</a></dialog>
<noscript><nav class="nojs-mobile">@foreach($hrefs as $key=>$href)@continue(($key==='projects'&&!$hasProjects)||($key==='news'&&!$hasNews)||($key==='profile'&&!$hasProfile))<a href="{{ $href }}">{{ __('site.'.$key) }}</a>@endforeach</nav></noscript>