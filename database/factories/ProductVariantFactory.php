<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return ['product_id' => Product::factory(), 'sku' => fake()->unique()->bothify('SY-####-US##'), 'size' => (string) fake()->randomElement([7, 8, 9, 10, 11]), 'color' => fake()->safeColorName(), 'stock_quantity' => 8, 'reserved_quantity' => 0, 'is_active' => true];
    }
}
