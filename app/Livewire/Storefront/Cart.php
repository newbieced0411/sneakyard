<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.storefront')]
#[Title('Your Bag | Sneakyard')]
final class Cart extends Component
{
    public function remove(int $variantId, CartService $cart): void
    {
        $cart->remove($variantId);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(int $variantId, int $quantity, CartService $cart): void
    {
        $variant = ProductVariant::query()->findOrFail($variantId);
        $cart->update($variant, $quantity);
        $this->dispatch('cart-updated');
    }

    public function render(CartService $cart): View
    {
        return view('livewire.storefront.cart', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'shipping' => $cart->shippingTotal(),
            'total' => $cart->total(),
        ]);
    }
}
