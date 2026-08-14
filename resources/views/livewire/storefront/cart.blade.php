<div class="cart-page">
    <header class="page-heading"><p class="eyebrow">Your selection</p><h1>Shopping bag</h1></header>
    @if($items->isEmpty())
        <div class="empty-state"><flux:icon.shopping-bag class="size-8" /><h2>Your bag is empty.</h2><p>Explore the latest verified pairs and find one worth keeping.</p><a class="primary-button" href="{{ route('shop') }}">Shop sneakers</a></div>
    @else
        <div class="cart-layout">
            <div class="cart-items">
                @foreach($items as $item)
                    <article class="cart-item" wire:key="cart-item-{{ $item['variant']->id }}">
                        <img src="{{ $item['variant']->product->primary_image_url }}" width="180" height="180" alt="{{ $item['variant']->product->name }}">
                        <div><p class="product-brand">{{ $item['variant']->product->brand->name }}</p><h2><a href="{{ route('products.show', $item['variant']->product) }}">{{ $item['variant']->product->name }}</a></h2><p>{{ $item['variant']->color }} · Size {{ $item['variant']->size }}</p><p class="product-price">₱{{ number_format($item['line_total'] / 100, 2) }}</p></div>
                        <div class="cart-item-actions">
                            <label><span>Quantity</span><select wire:change="updateQuantity({{ $item['variant']->id }}, $event.target.value)">@for($quantity = 1; $quantity <= min(5, $item['variant']->availableQuantity()); $quantity++)<option value="{{ $quantity }}" @selected($quantity === $item['quantity'])>{{ $quantity }}</option>@endfor</select></label>
                            <button type="button" class="text-link danger-link" wire:click="remove({{ $item['variant']->id }})">Remove</button>
                        </div>
                    </article>
                @endforeach
            </div>
            <aside class="order-summary"><h2>Order summary</h2><dl><div><dt>Subtotal</dt><dd>₱{{ number_format($subtotal / 100, 2) }}</dd></div><div><dt>Shipping</dt><dd>{{ $shipping ? '₱'.number_format($shipping / 100, 2) : 'Free' }}</dd></div><div class="summary-total"><dt>Total</dt><dd>₱{{ number_format($total / 100, 2) }}</dd></div></dl><a class="primary-button" href="{{ route('checkout') }}">Secure checkout</a><p>Taxes included. Cash on delivery available.</p></aside>
        </div>
    @endif
</div>
