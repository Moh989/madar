@props(['page'=>null,'item'=>null,'home'=>false,'preview'=>false,'title'=>null])
@php
$record=$item??$page;
$locale=app()->getLocale();
$metaTitle=$record?->tr('seo_title')?:($title?:($record?->tr('title')?:__('site.tagline')));
$description=$record?->tr('seo_description')?:($record?->tr('excerpt')?:__('site.intro_text'));
$canonical=rtrim(config('app.url'),'/').'/'.request()->path();
$other=$locale==='ar'?'en':'ar';
$switch=request()->route()&&str_starts_with(request()->path(),$locale)?preg_replace('#^/'.$locale.'(?=/|$)#','/'.$other,request()->getPathInfo()):'/'.$other;
if(request()->getQueryString())$switch.='?'.http_build_query(request()->query());
$ogImage=$item?->imageUrl()??($page?->imageUrl()?:(!$item?asset('images/air.webp'):null));
@endphp
<!doctype html><html lang="{{ $locale }}" dir="{{ $locale==='ar'?'rtl':'ltr' }}"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#10283F">
<title>{{ str_replace("\n",' ',$metaTitle) }} | {{ __('site.company') }}</title><meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}"><link rel="alternate" hreflang="{{ $locale }}" href="{{ $canonical }}"><link rel="alternate" hreflang="{{ $other }}" href="{{ rtrim(config('app.url'),'/').$switch }}"><link rel="alternate" hreflang="x-default" href="{{ rtrim(config('app.url'),'/').preg_replace('#^/(ar|en)#','/ar',request()->getPathInfo()) }}">
<meta property="og:title" content="{{ str_replace("\n",' ',$metaTitle) }}"><meta property="og:description" content="{{ $description }}"><meta property="og:type" content="website"><meta property="og:url" content="{{ $canonical }}">@if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif<meta property="og:locale" content="{{ $locale==='ar'?'ar_IQ':'en_US' }}"><meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="{{ $metaTitle }}"><meta name="twitter:description" content="{{ $description }}">@if($ogImage)<meta name="twitter:image" content="{{ $ogImage }}">@endif
@if($preview||!app()->isProduction())<meta name="robots" content="noindex,nofollow">@endif
<link rel="icon" type="image/png" href="{{ asset('images/company-mark.png') }}">
@vite(['resources/css/app.css','resources/js/app.js'])
<script type="application/ld+json">{!! json_encode(array_filter(['@context'=>'https://schema.org','@type'=>'Organization','name'=>__('site.legal_name'),'url'=>config('app.url'),'email'=>$contact['email']??null]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP) !!}</script>
</head><body class="{{ $home?'is-home':'is-inner' }}">
<a class="skip" href="#main">{{ __('site.skip') }}</a>
<x-nav :home="$home" :switch="$switch" />
@if($preview)<div class="preview-banner">{{ __('site.preview') }}</div>@endif
<main id="main" tabindex="-1">{{ $slot }}</main>
<x-footer />
</body></html>