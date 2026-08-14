<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = (string) config('sneakyard.admin.email');
        $adminPassword = (string) config('sneakyard.admin.password');

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            ['name' => 'Sneakyard Admin', 'role' => UserRole::Admin, 'password' => Hash::make($adminPassword)],
        );

        if (! config('sneakyard.seed_demo_catalog')) {
            return;
        }

        $brands = collect(['Sneakyard Select', 'Archive Athletics', 'Heritage Lab'])->mapWithKeys(function (string $name): array {
            $brand = Brand::query()->updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => 'Verified pairs sourced through trusted retail partners.', 'is_active' => true]);

            return [$name => $brand];
        });

        $categories = collect(['Court', 'Lifestyle', 'Running'])->mapWithKeys(function (string $name): array {
            $category = Category::query()->updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => "Authentic {$name} sneakers.", 'is_active' => true]);

            return [$name => $category];
        });

        $products = [
            ['name' => 'Classic Court Low', 'sku' => 'SY-CCL-WW', 'brand' => 'Sneakyard Select', 'category' => 'Court', 'price' => 699500, 'color' => 'White / White', 'image' => 'images/products/classic-court-low-white.png', 'description' => 'A clean all-leather court silhouette with cushioned support and an easy everyday profile. Every pair is individually inspected before dispatch.'],
            ['name' => 'Retro 86 Low', 'sku' => 'SY-R86-BW', 'brand' => 'Archive Athletics', 'category' => 'Lifestyle', 'price' => 799500, 'color' => 'Black / White', 'image' => 'images/products/retro-86-low-black-white.png', 'description' => 'A bold black-and-white low top inspired by late-eighties basketball lines, finished with premium leather panels and a stable cupsole.'],
            ['name' => 'Heritage Runner', 'sku' => 'SY-HR-GB', 'brand' => 'Heritage Lab', 'category' => 'Running', 'price' => 849500, 'color' => 'Grey / Bone', 'image' => 'images/products/heritage-runner-grey-bone.png', 'description' => 'Layered suede and breathable mesh meet a sculpted comfort sole in this refined retro runner, authenticated and ready for daily wear.'],
            ['name' => 'Suede Campus 00S', 'sku' => 'SY-SC-WW', 'brand' => 'Archive Athletics', 'category' => 'Lifestyle', 'price' => 699500, 'color' => 'Wonder White', 'image' => 'images/products/suede-campus-wonder-white.png', 'description' => 'Soft wonder-white suede, relaxed proportions, and a gum-toned outsole deliver a collectible campus look with versatile everyday comfort.'],
        ];

        foreach ($products as $position => $data) {
            $product = Product::query()->updateOrCreate(['sku' => $data['sku']], [
                'brand_id' => $brands[$data['brand']]->id, 'category_id' => $categories[$data['category']]->id,
                'name' => $data['name'], 'slug' => Str::slug($data['name']), 'description' => $data['description'],
                'price' => $data['price'], 'gender' => 'unisex', 'status' => ProductStatus::Active,
                'is_featured' => true, 'meta_title' => $data['name'].' — Authentic Sneakers | Sneakyard',
                'meta_description' => Str::limit($data['description'], 155, ''), 'published_at' => now()->subMinutes(10 - $position),
            ]);

            $product->images()->updateOrCreate(['path' => $data['image']], ['alt_text' => $data['name'].' in '.$data['color'], 'sort_order' => 0, 'is_primary' => true]);

            foreach (['7', '8', '9', '10', '11'] as $size) {
                $product->variants()->updateOrCreate(['sku' => $data['sku'].'-US'.$size], ['size' => $size, 'color' => $data['color'], 'stock_quantity' => 8 - $position, 'reserved_quantity' => 0, 'is_active' => true]);
            }
        }
    }
}
