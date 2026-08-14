<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_only_active_featured_products(): void
    {
        $active = Product::factory()->featured()->create(['name' => 'Verified Court Low']);
        ProductImage::factory()->for($active)->create();
        ProductVariant::factory()->for($active)->create();
        Product::factory()->draft()->featured()->create(['name' => 'Hidden Draft Pair']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Authentic,')
            ->assertSee('Verified Court Low')
            ->assertDontSee('Hidden Draft Pair')
            ->assertSee('manifest.webmanifest');
    }

    public function test_product_page_contains_inventory_and_structured_data(): void
    {
        $product = Product::factory()->create(['name' => 'Archive Runner', 'meta_title' => 'Archive Runner Authentic']);
        ProductImage::factory()->for($product)->create();
        ProductVariant::factory()->for($product)->create(['size' => '9', 'stock_quantity' => 5]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Archive Runner Authentic')
            ->assertSee('Select size')
            ->assertSee('application/ld+json', false)
            ->assertSee('Verified authentic');
    }

    public function test_draft_product_cannot_be_viewed(): void
    {
        $product = Product::factory()->draft()->create();

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_sitemap_and_meta_catalog_feed_include_active_products(): void
    {
        $product = Product::factory()->create(['name' => 'Meta Ready Pair']);
        ProductImage::factory()->for($product)->create();
        ProductVariant::factory()->for($product)->create();

        $this->get(route('sitemap'))->assertOk()->assertSee(route('products.show', $product), false);
        $feed = $this->get(route('feeds.meta'));
        $feed->assertOk();
        $this->assertStringContainsString('Meta Ready Pair', $feed->streamedContent());
        $this->assertStringContainsString('PHP', $feed->streamedContent());
    }
}
