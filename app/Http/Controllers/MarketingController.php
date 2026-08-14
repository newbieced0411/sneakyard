<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MarketingController extends Controller
{
    public function sitemap(): Response
    {
        $products = Product::query()->active()->latest('updated_at')->get(['slug', 'updated_at']);

        return response()
            ->view('marketing.sitemap', compact('products'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        return response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".route('sitemap')."\n")
            ->header('Content-Type', 'text/plain');
    }

    public function metaFeed(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand']);

            Product::query()->active()->with(['brand', 'primaryImage', 'variants'])->chunkById(200, function ($products) use ($stream): void {
                foreach ($products as $product) {
                    fputcsv($stream, [
                        $product->sku, $product->name, $product->description,
                        $product->variants->sum('stock_quantity') > 0 ? 'in stock' : 'out of stock',
                        'new', number_format($product->price / 100, 2, '.', '').' PHP',
                        route('products.show', $product), $product->primary_image_url, $product->brand->name,
                    ]);
                }
            });

            fclose($stream);
        }, 'sneakyard-meta-catalog.csv', ['Content-Type' => 'text/csv']);
    }
}
