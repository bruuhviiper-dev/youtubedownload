<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', __('subtitle'))">
    <meta name="keywords" content="youtube downloader, download youtube video, youtube mp4, youtube mp3, baixar video youtube, youtube video downloader">
    <title>@yield('title', __('title'))</title>
    
    <!-- Canonical & Hreflang for SEO -->
    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="x-default" href="{{ route('home') }}" />
    <link rel="alternate" hreflang="pt" href="{{ route('home.locale', ['locale' => 'pt']) }}" />
    <link rel="alternate" hreflang="en" href="{{ route('home.locale', ['locale' => 'en']) }}" />
    
    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="@yield('title', __('title'))">
    <meta property="og:description" content="@yield('meta_description', __('subtitle'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', __('title'))">
    <meta name="twitter:description" content="@yield('meta_description', __('subtitle'))">
    
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/app.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "WebApplication",
      "name": "TubeLift",
      "url": "{{ url('/') }}",
      "description": "{{ __('subtitle') }}",
      "applicationCategory": "MultimediaApplication",
      "operatingSystem": "All",
      "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      }
    }
    </script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    @stack('head')
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container" style="display:flex; justify-content:space-between; align-items:center;">
        <a href="/" class="logo">
            <svg class="logo-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.582 6.186a2.506 2.506 0 0 0-1.762-1.766C18.265 4 12 4 12 4s-6.264 0-7.818.42a2.506 2.506 0 0 0-1.764 1.766C2 7.74 2 12 2 12s0 4.262.418 5.814a2.506 2.506 0 0 0 1.764 1.766C5.736 20 12 20 12 20s6.265 0 7.82-.42a2.506 2.506 0 0 0 1.762-1.766C22 16.26 22 12 22 12s0-4.26-.418-5.814zM9.995 15.25V8.75L15.67 12l-5.675 3.25z"/>
            </svg>
            <span class="logo-text">Tube<span>Lift</span></span>
        </a>
        <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Dark Mode">
            <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
            <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </button>
    </div>
</nav>

<main>
    @yield('content')
</main>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-logo">Tube<span>Lift</span></div>
        <div class="footer-text">{{ __('footer_copyright') }}</div>
        <div class="footer-links">
            <a href="{{ route('policy') }}">{{ __('link_privacy') }}</a>
            <a href="{{ route('terms') }}">{{ __('link_terms') }}</a>
            <a href="{{ route('dmca') }}">{{ __('link_dmca') }}</a>
        </div>
    </div>
</footer>

<script>
function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    }
}
</script>
@stack('scripts')

</body>
</html>
