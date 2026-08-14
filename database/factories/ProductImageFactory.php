<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return ['product_id' => Product::factory(), 'path' => 'images/products/classic-court-low-white.png', 'alt_text' => 'Authentic sneaker product image', 'sort_order' => 0, 'is_primary' => true];
    }
}
