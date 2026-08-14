<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

final class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->status->value === 'active' && $product->published_at?->isPast(), 404);

        $product->load([
            'brand',
            'category',
            'images',
            'variants' => fn ($query) => $query->available()->orderByRaw('CAST(size AS DECIMAL(5,1))'),
        ]);

        $related = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with(['brand', 'primaryImage'])
            ->limit(4)
            ->get();

        return view('storefront.product', compact('product', 'related'));
    }
}
