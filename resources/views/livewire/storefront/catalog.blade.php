<div class="catalog-page">
    <header class="catalog-hero"><p class="eyebrow">Verified authentic</p><h1>Find your next pair.</h1><p>Curated footwear, clear inventory, no guesswork.</p></header>

    <section class="catalog-toolbar" aria-label="Product filters">
        <label class="search-field"><span>Search</span><input type="search" wire:model.live.debounce.300ms="search" placeholder="Search sneakers or SKU"></label>
        <label><span>Brand</span><select wire:model.live="brand"><option value="">All brands</option>@foreach($brands as $item)<option value="{{ $item->slug }}">{{ $item->name }}</option>@endforeach</select></label>
        <label><span>For</span><select wire:model.live="gender"><option value="">Everyone</option><option value="men">Men</option><option value="women">Women</option><option value="unisex">Unisex</option></select></label>
        <label><span>Sort</span><select wire:model.live="sort"><option value="latest">Latest</option><option value="price-low">Price: low to high</option><option value="price-high">Price: high to low</option></select></label>
        @if($search || $brand || $gender)<button type="button" class="secondary-button" wire:click="clearFilters">Clear filters</button>@endif
    </section>

    <div class="loading-line" wire:loading aria-label="Loading products"></div>

    @if($products->isEmpty())
        <div class="empty-state"><flux:icon.magnifying-glass class="size-8" /><h2>No pairs found.</h2><p>Try a broader search or clear the filters.</p><button type="button" class="text-link" wire:click="clearFilters">Reset filters</button></div>
    @else
        <div class="product-grid catalog-grid">
            @foreach($products as $product)
                <x-product-card :product="$product" wire:key="product-{{ $product->id }}" />
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $products->links() }}</div>
    @endif
</div>
