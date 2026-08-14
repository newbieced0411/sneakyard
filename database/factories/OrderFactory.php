<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = 699500;

        return ['public_id' => (string) Str::uuid(), 'order_number' => 'SY-'.now()->format('ymd').'-'.fake()->unique()->numerify('####'), 'status' => OrderStatus::Pending, 'payment_status' => PaymentStatus::Unpaid, 'payment_method' => 'cod', 'customer_name' => fake()->name(), 'customer_email' => fake()->safeEmail(), 'customer_phone' => fake()->phoneNumber(), 'shipping_address' => fake()->streetAddress(), 'shipping_city' => fake()->city(), 'shipping_province' => 'Metro Manila', 'shipping_postal_code' => '1000', 'subtotal' => $subtotal, 'shipping_total' => 0, 'discount_total' => 0, 'grand_total' => $subtotal, 'placed_at' => now()];
    }
}
