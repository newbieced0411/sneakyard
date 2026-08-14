@props(['product'])
<article {{ $attributes->class('product-card') }}>
    <a href="{{ route('products.show', $product) }}" class="product-image-link" aria-label="View {{ $product->name }}">
        <img src="{{ $product->primary_image_url }}" width="640" height="640" alt="{{ $product->primaryImage?->alt_text ?? $product->name }}" loading="lazy">
    </a>
    <div class="product-card-body">
        <p class="product-brand">{{ $product->brand->name }}</p>
        <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        <p class="product-color">{{ $product->variants->first()?->color ?? 'Multiple colors' }}</p>
        <p class="product-price">{{ $product->formatted_price }}</p>
    </div>
</article>
