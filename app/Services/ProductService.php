<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductStatus;
use App\Jobs\SyncProductToMeta;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ProductService
{
    /** @param array<string, mixed> $data */
    public function create(array $data, UploadedFile $image): Product
    {
        $product = DB::transaction(function () use ($data, $image): Product {
            $product = Product::query()->create($this->productAttributes($data));
            $this->syncVariants($product, $data['variants']);
            $this->storePrimaryImage($product, $image);

            return $product->load(['brand', 'category', 'variants', 'images']);
        });

        SyncProductToMeta::dispatch($product)->afterCommit();

        return $product;
    }

    /** @param array<string, mixed> $data */
    public function update(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        $product = DB::transaction(function () use ($product, $data, $image): Product {
            $product->update($this->productAttributes($data, $product));
            $this->syncVariants($product, $data['variants']);

            if ($image) {
                $this->storePrimaryImage($product, $image);
            }

            return $product->fresh(['brand', 'category', 'variants', 'images']);
        });

        SyncProductToMeta::dispatch($product)->afterCommit();

        return $product;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function productAttributes(array $data, ?Product $product = null): array
    {
        $status = ProductStatus::from($data['status']);

        return [
            ...Arr::only($data, [
                'brand_id', 'category_id', 'name', 'sku', 'description', 'gender', 'status',
                'is_featured', 'meta_title', 'meta_description',
            ]),
            'slug' => $this->uniqueSlug($data['name'], $product),
            'price' => (int) round(((float) $data['price']) * 100),
            'compare_at_price' => filled($data['compare_at_price'] ?? null)
                ? (int) round(((float) $data['compare_at_price']) * 100)
                : null,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'published_at' => $status === ProductStatus::Active ? ($product?->published_at ?? now()) : null,
        ];
    }

    /** @param array<int, array<string, mixed>> $variants */
    private function syncVariants(Product $product, array $variants): void
    {
        $keptIds = [];

        foreach ($variants as $variantData) {
            $variant = $product->variants()->updateOrCreate(
                ['id' => $variantData['id'] ?? null],
                [
                    'sku' => $variantData['sku'],
                    'size' => $variantData['size'],
                    'color' => $variantData['color'],
                    'stock_quantity' => (int) $variantData['stock_quantity'],
                    'reserved_quantity' => 0,
                    'is_active' => true,
                ],
            );
            $keptIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keptIds)->delete();
    }

    private function storePrimaryImage(Product $product, UploadedFile $image): void
    {
        $path = $image->store('products', 'public');
        $product->images()->update(['is_primary' => false]);
        $product->images()->create([
            'path' => 'storage/'.$path,
            'alt_text' => $product->name.' authentic sneaker',
            'sort_order' => 0,
            'is_primary' => true,
        ]);
    }

    private function uniqueSlug(string $name, ?Product $product = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Product::query()->when($product, fn ($query) => $query->whereKeyNot($product->id))->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
