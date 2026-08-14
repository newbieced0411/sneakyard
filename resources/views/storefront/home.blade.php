<x-layouts.storefront>
    <section class="home-hero" aria-labelledby="hero-title">
        <div class="hero-copy">
            <p class="eyebrow">Legit. Original. Verified.</p>
            <h1 id="hero-title">Authentic,<br>always.</h1>
            <p>We bring you 100% original sneakers from trusted global retailers. No fakes. No compromises.</p>
            <a class="primary-button" href="{{ route('shop') }}">Shop new drops</a>
        </div>
        <img src="{{ asset('images/storefront/hero-authentic-always.png') }}" width="1120" height="1400" alt="Two off-white leather sneakers on warm stone blocks" fetchpriority="high">
    </section>

    <section class="product-section" aria-labelledby="best-sellers-heading">
        <div class="section-heading-row">
            <h2 id="best-sellers-heading" class="section-label">Best sellers</h2>
            <a class="text-link" href="{{ route('shop') }}">View all <flux:icon.arrow-right class="size-4" /></a>
        </div>
        <div class="product-grid">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="empty-state"><h3>New pairs are landing soon.</h3><p>Check back shortly for the first verified Sneakyard drop.</p></div>
            @endforelse
        </div>
    </section>

    <section id="authenticity" class="trust-section" aria-labelledby="trust-heading">
        <h2 id="trust-heading" class="section-label">The Sneakyard standard</h2>
        <div class="trust-grid">
            <article><flux:icon.check-badge class="trust-icon" /><h3>100% authentic</h3><p>Every pair is verified and sourced only from authorized retailers and trusted partners.</p></article>
            <article><flux:icon.truck class="trust-icon" /><h3>Fast & reliable</h3><p>Trackable nationwide delivery, carefully packed and dispatched with regular updates.</p></article>
            <article><flux:icon.arrow-path-rounded-square class="trust-icon" /><h3>Easy returns</h3><p>Eligible unworn pairs can be returned within seven days for a straightforward exchange.</p></article>
        </div>
    </section>

    <section class="lifestyle-panel" aria-label="Sneakyard sneakers in everyday style">
        <img src="{{ asset('images/storefront/lifestyle-authentic.png') }}" width="1440" height="630" alt="Off-white sneakers styled with black trousers on sunlit concrete" loading="lazy">
    </section>

    <section class="service-strip" aria-label="Store assurances">
        <span>Shop with confidence</span><span>100% authentic</span><span>Fast shipping</span><span>Easy returns</span>
    </section>

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            chr(64).'context' => 'https://schema.org', '@type' => 'WebSite', 'name' => 'Sneakyard',
            'url' => route('home'), 'potentialAction' => ['@type' => 'SearchAction', 'target' => route('shop').'?search={search_term_string}', 'query-input' => 'required name=search_term_string'],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layouts.storefront>
