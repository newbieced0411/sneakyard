<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

final class AddToCart extends Component
{
    public Product $product;

    public ?int $variantId = null;

    public int $quantity = 1;

    public bool $added = false;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->variantId = $product->variants->first()?->id;
    }

    public function add(CartService $cart): void
    {
        $this->validate([
            'variantId' => [
                'required',
                'integer',
                Rule::exists('product_variants', 'id')->where('product_id', $this->product->id),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $variant = ProductVariant::query()->findOrFail($this->variantId);
        $cart->add($variant, $this->quantity);
        $this->added = true;
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.storefront.add-to-cart');
    }
}
