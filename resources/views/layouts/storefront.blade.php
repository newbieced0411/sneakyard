@props(['metaTitle' => null, 'metaDescription' => null, 'ogType' => 'website', 'ogImage' => null])
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111111">
    <meta name="description" content="{{ $metaDescription ?? 'Shop legit, original, and authenticated sneakers from Sneakyard. Nationwide delivery across the Philippines.' }}">
    <meta property="og:site_name" content="Sneakyard">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $metaTitle ?? ($title ?? 'Sneakyard — Authentic, Always.') }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Legit sneakers. Verified authentic. Delivered nationwide.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/storefront/hero-authentic-always.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/icons/icon-192.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|playfair-display:500,600|special-elite:400" rel="stylesheet">
    <title>{{ $metaTitle ?? ($title ?? 'Sneakyard — Authentic, Always.') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
    @stack('head')

    @if(config('sneakyard.meta.pixel_id'))
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,
            'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', @js(config('sneakyard.meta.pixel_id'))); fbq('track', 'PageView');
        </script>
    @endif
</head>
<body class="storefront-body" x-data="{ mobileMenu: false, offline: !navigator.onLine }" @online.window="offline = false" @offline.window="offline = true">
    <a class="skip-link" href="#main-content">Skip to content</a>

    <div x-show="offline" x-cloak class="offline-banner" role="status">
        You’re offline. Saved pages remain available; checkout will resume when you reconnect.
    </div>

    <div class="announcement-bar">100% authentic sneakers. Fast shipping nationwide.</div>

    <header class="storefront-header">
        <button type="button" class="icon-button mobile-only" @click="mobileMenu = !mobileMenu" aria-label="Open navigation" :aria-expanded="mobileMenu">
            <flux:icon.bars-3 class="size-5" />
        </button>

        <nav class="desktop-nav" aria-label="Primary navigation">
            <a href="{{ route('shop', ['gender' => 'men']) }}">Men</a>
            <a href="{{ route('shop', ['gender' => 'women']) }}">Women</a>
            <a href="{{ route('shop') }}">Brands</a>
            <a href="{{ route('shop') }}">New drops</a>
        </nav>

        <a class="wordmark" href="{{ route('home') }}" aria-label="Sneakyard home"><x-brand-logo /></a>

        <nav class="header-actions" aria-label="Shopping actions">
            <a class="icon-button" href="{{ route('shop') }}" aria-label="Search sneakers"><flux:icon.magnifying-glass class="size-5" /></a>
            <a class="icon-button desktop-only" href="{{ route('admin.login') }}" aria-label="Account"><flux:icon.user class="size-5" /></a>
            <a class="icon-button cart-link" href="{{ route('cart') }}" aria-label="Shopping bag">
                <flux:icon.shopping-bag class="size-5" />
                <livewire:storefront.cart-count />
            </a>
        </nav>

        <nav x-show="mobileMenu" x-transition.opacity @click.outside="mobileMenu = false" x-cloak class="mobile-menu" aria-label="Mobile navigation">
            <a href="{{ route('shop') }}">Shop all</a>
            <a href="{{ route('shop', ['gender' => 'men']) }}">Men</a>
            <a href="{{ route('shop', ['gender' => 'women']) }}">Women</a>
            <a href="{{ route('shop') }}">New drops</a>
            <a href="#authenticity">Our authenticity promise</a>
        </nav>
    </header>

    @if(session('success'))
        <div class="flash-message" role="status">{{ session('success') }}</div>
    @endif

    <main id="main-content">{{ $slot }}</main>

    <footer class="storefront-footer">
        <div class="footer-grid">
            <div>
                <a class="wordmark footer-wordmark" href="{{ route('home') }}" aria-label="Sneakyard home"><x-brand-logo /></a>
                <p>Legit pairs, carefully sourced and verified for sneaker collectors across the Philippines.</p>
            </div>
            <div><h2>Shop</h2><a href="{{ route('shop') }}">All sneakers</a><a href="{{ route('shop') }}">New drops</a><a href="{{ route('shop', ['gender' => 'men']) }}">Men</a><a href="{{ route('shop', ['gender' => 'women']) }}">Women</a></div>
            <div><h2>Help</h2><a href="#authenticity">Authenticity</a><a href="mailto:hello@sneakyard.ph">Contact us</a><a href="{{ route('cart') }}">Delivery & returns</a></div>
            <div><h2>Follow</h2><a href="{{ config('services.facebook.page_url', '#') }}" rel="noopener">Facebook</a><a href="#">Instagram</a><button class="install-link" type="button" data-install-pwa hidden>Install app</button></div>
        </div>
        <div class="footer-bottom"><span>© {{ date('Y') }} Sneakyard.</span><span>Authentic, always.</span></div>
    </footer>

    <nav class="mobile-bottom-nav" aria-label="Mobile app navigation">
        <a href="{{ route('home') }}"><flux:icon.home class="size-5" /><span>Home</span></a>
        <a href="{{ route('shop') }}"><flux:icon.rectangle-group class="size-5" /><span>Shop</span></a>
        <a href="{{ route('shop') }}"><flux:icon.magnifying-glass class="size-5" /><span>Search</span></a>
        <a href="{{ route('cart') }}"><flux:icon.shopping-bag class="size-5" /><span>Bag</span></a>
    </nav>

    @livewireScripts
    @fluxScripts
    @stack('scripts')
</body>
</html>
