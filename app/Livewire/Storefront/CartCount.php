<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class CartCount extends Component
{
    #[On('cart-updated')]
    public function refreshCount(): void {}

    public function render(CartService $cart): View
    {
        return view('livewire.storefront.cart-count', ['count' => $cart->count()]);
    }
}
