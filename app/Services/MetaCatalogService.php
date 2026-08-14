<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class MetaCatalogService
{
    public function isConfigured(): bool
    {
        return filled(config('sneakyard.meta.catalog_id')) && filled(config('sneakyard.meta.access_token'));
    }

    public function sync(Product $product): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $product->loadMissing(['brand', 'primaryImage']);
        $endpoint = sprintf(
            'https://graph.facebook.com/%s/%s/products',
            config('sneakyard.meta.graph_version'),
            config('sneakyard.meta.catalog_id'),
        );

        $response = $this->client()->post($endpoint, [
            'retailer_id' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'availability' => $product->variants()->available()->exists() ? 'in stock' : 'out of stock',
            'condition' => 'new',
            'price' => $product->price,
            'currency' => 'PHP',
            'url' => route('products.show', $product),
            'image_url' => $product->primary_image_url,
            'brand' => $product->brand->name,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Meta Catalog sync failed: '.$response->body());
        }

        return (string) $response->json('id');
    }

    private function client(): PendingRequest
    {
        return Http::asForm()
            ->acceptJson()
            ->withToken((string) config('sneakyard.meta.access_token'))
            ->timeout(15)
            ->retry(3, 500);
    }
}
