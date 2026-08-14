<x-layouts.storefront :meta-title="$product->meta_title ?: $product->name.' | Sneakyard'" :meta-description="$product->meta_description ?: str($product->description)->limit(155)" og-type="product" :og-image="$product->primary_image_url">
    <div class="product-page">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('shop') }}">Sneakers</a><span>/</span><span>{{ $product->name }}</span></nav>
        <div class="product-detail-grid">
            <div class="product-gallery">
                @foreach($product->images as $image)
                    <img src="{{ asset($image->path) }}" width="900" height="900" alt="{{ $image->alt_text }}" @if(!$loop->first) loading="lazy" @endif>
                @endforeach
            </div>
            <div class="product-info">
                <p class="eyebrow">{{ $product->brand->name }}</p>
                <h1>{{ $product->name }}</h1>
                <p class="detail-price">{{ $product->formatted_price }}</p>
                <p class="product-description">{{ $product->description }}</p>
                <livewire:storefront.add-to-cart :product="$product" />
                <div class="detail-assurances">
                    <p><flux:icon.check-badge class="size-5" /> Verified authentic</p>
                    <p><flux:icon.truck class="size-5" /> Nationwide tracked delivery</p>
                    <p><flux:icon.arrow-path class="size-5" /> Seven-day eligible returns</p>
                </div>
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="product-section related-section"><div class="section-heading-row"><h2 class="section-label">You may also like</h2></div><div class="product-grid">@foreach($related as $item)<x-product-card :product="$item" />@endforeach</div></section>
    @endif

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            chr(64).'context' => 'https://schema.org', '@type' => 'Product', 'name' => $product->name,
            'image' => $product->images->map(fn($image) => asset($image->path))->all(), 'description' => $product->description,
            'sku' => $product->sku, 'brand' => ['@type' => 'Brand', 'name' => $product->brand->name],
            'offers' => ['@type' => 'Offer', 'priceCurrency' => 'PHP', 'price' => number_format($product->price / 100, 2, '.', ''), 'availability' => $product->variants->isNotEmpty() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock', 'url' => route('products.show', $product)],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layouts.storefront>
