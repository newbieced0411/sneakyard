<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->active()
            ->with(['brand', 'category', 'primaryImage', 'variants' => fn ($query) => $query->available()])
            ->search($request->string('search')->toString())
            ->latest('published_at')
            ->paginate(min(48, max(1, $request->integer('per_page', 12))));

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        abort_unless($product->status->value === 'active' && $product->published_at?->isPast(), 404);

        return new ProductResource($product->load(['brand', 'category', 'images', 'variants' => fn ($query) => $query->available()]));
    }
}
