<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => ['amount' => $this->price, 'currency' => 'PHP', 'formatted' => $this->formatted_price],
            'gender' => $this->gender,
            'brand' => $this->whenLoaded('brand', fn () => ['id' => $this->brand->id, 'name' => $this->brand->name, 'slug' => $this->brand->slug]),
            'category' => $this->whenLoaded('category', fn () => ['id' => $this->category->id, 'name' => $this->category->name, 'slug' => $this->category->slug]),
            'primary_image' => $this->primary_image_url,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image) => ['url' => asset($image->path), 'alt' => $image->alt_text])),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'url' => route('products.show', $this->resource),
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
