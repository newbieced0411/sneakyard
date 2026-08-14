<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final readonly class OrderService
{
    public function __construct(private CartService $cart) {}

    /** @param array<string, mixed> $customer */
    public function place(array $customer, ?User $user = null): Order
    {
        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your bag is empty.']);
        }

        $order = DB::transaction(function () use ($cartItems, $customer, $user): Order {
            $lockedVariants = ProductVariant::query()
                ->with('product')
                ->whereIn('id', $cartItems->pluck('variant.id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $item) {
                $variant = $lockedVariants->get($item['variant']->id);

                if (! $variant || $variant->availableQuantity() < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "{$item['variant']->product->name} is no longer available in the requested quantity.",
                    ]);
                }
            }

            $subtotal = (int) $cartItems->sum('line_total');
            $shipping = $subtotal >= config('sneakyard.free_shipping_threshold')
                ? 0
                : (int) config('sneakyard.shipping_fee');

            $order = Order::query()->create([
                'public_id' => (string) Str::uuid(),
                'order_number' => $this->nextOrderNumber(),
                'user_id' => $user?->id,
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'payment_method' => $customer['payment_method'] ?? 'cod',
                'customer_name' => $customer['customer_name'],
                'customer_email' => $customer['customer_email'],
                'customer_phone' => $customer['customer_phone'],
                'shipping_address' => $customer['shipping_address'],
                'shipping_city' => $customer['shipping_city'],
                'shipping_province' => $customer['shipping_province'],
                'shipping_postal_code' => $customer['shipping_postal_code'],
                'subtotal' => $subtotal,
                'shipping_total' => $shipping,
                'discount_total' => 0,
                'grand_total' => $subtotal + $shipping,
                'customer_notes' => $customer['customer_notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($cartItems as $item) {
                /** @var ProductVariant $variant */
                $variant = $lockedVariants->get($item['variant']->id);
                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'unit_price' => $variant->product->price,
                    'quantity' => $item['quantity'],
                    'line_total' => $variant->product->price * $item['quantity'],
                ]);

                $variant->decrement('stock_quantity', $item['quantity']);
            }

            return $order->load('items');
        });

        $this->cart->clear();
        event(new OrderPlaced($order));

        User::query()->where('role', 'admin')->each(
            fn (User $admin) => $admin->notify(new OrderPlacedNotification($order)),
        );

        return $order;
    }

    private function nextOrderNumber(): string
    {
        $now = now();
        $orderDate = $now->toDateString();

        DB::table('order_number_sequences')->insertOrIgnore([
            'order_date' => $orderDate,
            'last_number' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lastNumber = DB::table('order_number_sequences')
            ->where('order_date', $orderDate)
            ->lockForUpdate()
            ->value('last_number');
        $sequence = ((int) $lastNumber) + 1;

        DB::table('order_number_sequences')
            ->where('order_date', $orderDate)
            ->update([
                'last_number' => $sequence,
                'updated_at' => $now,
            ]);

        $prefix = 'SY-'.$now->format('ymd').'-';

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
