<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::query()
            ->active()
            ->featured()
            ->with(['brand', 'primaryImage', 'variants' => fn ($query) => $query->available()])
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('storefront.home', compact('products'));
    }
}
