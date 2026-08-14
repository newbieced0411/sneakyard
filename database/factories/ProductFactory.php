<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(3, true));

        return [
            'brand_id' => Brand::factory(), 'category_id' => Category::factory(), 'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999), 'sku' => fake()->unique()->bothify('SY-####-??'),
            'description' => fake()->paragraph(), 'price' => fake()->numberBetween(450000, 1400000),
            'compare_at_price' => null, 'gender' => 'unisex', 'status' => ProductStatus::Active,
            'is_featured' => false, 'meta_title' => null, 'meta_description' => null, 'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ProductStatus::Draft, 'published_at' => null]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
