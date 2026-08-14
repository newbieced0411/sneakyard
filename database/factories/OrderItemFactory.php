<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

final class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return ['order_id' => Order::factory(), 'product_id' => Product::factory(), 'product_variant_id' => ProductVariant::factory(), 'product_name' => 'Classic Court Low', 'sku' => fake()->unique()->bothify('SY-####-US##'), 'size' => '9', 'color' => 'White / White', 'unit_price' => 699500, 'quantity' => 1, 'line_total' => 699500];
    }
}
