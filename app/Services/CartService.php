<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final readonly class CartService
{
    private const SESSION_KEY = 'sneakyard.cart';

    public function add(ProductVariant $variant, int $quantity = 1): void
    {
        $variant->loadMissing(['product.primaryImage']);
        $quantity = max(1, $quantity);
        $cart = session()->get(self::SESSION_KEY, []);
        $existing = (int) ($cart[$variant->id]['quantity'] ?? 0);

        if ($existing + $quantity > $variant->availableQuantity()) {
            throw ValidationException::withMessages([
                'quantity' => 'Only '.$variant->availableQuantity().' pair(s) are available in this size.',
            ]);
        }

        $cart[$variant->id] = ['quantity' => $existing + $quantity];
        session()->put(self::SESSION_KEY, $cart);
    }

    public function update(ProductVariant $variant, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($variant->id);

            return;
        }

        if ($quantity > $variant->availableQuantity()) {
            throw ValidationException::withMessages([
                'quantity' => 'Only '.$variant->availableQuantity().' pair(s) are available in this size.',
            ]);
        }

        $cart = session()->get(self::SESSION_KEY, []);
        $cart[$variant->id] = ['quantity' => $quantity];
        session()->put(self::SESSION_KEY, $cart);
    }

    public function remove(int $variantId): void
    {
        $cart = session()->get(self::SESSION_KEY, []);
        unset($cart[$variantId]);
        session()->put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /** @return Collection<int, array{variant: ProductVariant, quantity: int, line_total: int}> */
    public function items(): Collection
    {
        $cart = session()->get(self::SESSION_KEY, []);

        if ($cart === []) {
            return collect();
        }

        $variants = ProductVariant::query()
            ->with(['product.brand', 'product.primaryImage'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)->map(function (array $item, int|string $variantId) use ($variants): ?array {
            $variant = $variants->get((int) $variantId);

            if (! $variant) {
                return null;
            }

            $quantity = (int) $item['quantity'];

            return [
                'variant' => $variant,
                'quantity' => $quantity,
                'line_total' => $variant->product->price * $quantity,
            ];
        })->filter()->values();
    }

    public function count(): int
    {
        return (int) collect(session()->get(self::SESSION_KEY, []))->sum('quantity');
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum('line_total');
    }

    public function shippingTotal(): int
    {
        $subtotal = $this->subtotal();

        return $subtotal >= config('sneakyard.free_shipping_threshold') ? 0 : (int) config('sneakyard.shipping_fee');
    }

    public function total(): int
    {
        return $this->subtotal() + $this->shippingTotal();
    }
}
