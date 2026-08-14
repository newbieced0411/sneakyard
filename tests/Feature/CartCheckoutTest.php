<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\OrderPlaced;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_stock_safe_order_and_admin_is_notified(): void
    {
        Notification::fake();
        Event::fake([OrderPlaced::class]);
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Classic Court Low', 'price' => 699500]);
        ProductImage::factory()->for($product)->create();
        $variant = ProductVariant::factory()->for($product)->create(['size' => '9', 'stock_quantity' => 4]);

        $this->withSession([]);
        $cart = app(CartService::class);
        $cart->add($variant, 2);

        $order = app(OrderService::class)->place([
            'customer_name' => 'Juan Dela Cruz',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '09171234567',
            'shipping_address' => '123 Sneaker Street',
            'shipping_city' => 'Manila',
            'shipping_province' => 'Metro Manila',
            'shipping_postal_code' => '1000',
            'payment_method' => 'cod',
        ]);

        $this->assertSame('SY-'.now()->format('ymd').'-0001', $order->order_number);
        $this->assertSame(1399000, $order->subtotal);
        $this->assertSame(0, $order->shipping_total);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'quantity' => 2, 'size' => '9']);
        $this->assertDatabaseHas('customers', ['email' => 'juan@example.com', 'name' => 'Juan Dela Cruz']);
        $this->assertNotNull($order->customer_id);
        $this->assertSame(2, $variant->fresh()->stock_quantity);
        $this->assertSame(0, $cart->count());
        Event::assertDispatched(OrderPlaced::class, fn (OrderPlaced $event) => $event->order->is($order));
        Notification::assertSentTo($admin, OrderPlacedNotification::class);
    }

    public function test_order_numbers_increment_without_aggregate_row_locks(): void
    {
        Notification::fake();
        Event::fake([OrderPlaced::class]);
        $product = Product::factory()->create(['price' => 699500]);
        $variant = ProductVariant::factory()->for($product)->create(['stock_quantity' => 3]);
        $customer = [
            'customer_name' => 'Juan Dela Cruz',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '09171234567',
            'shipping_address' => '123 Sneaker Street',
            'shipping_city' => 'Manila',
            'shipping_province' => 'Metro Manila',
            'shipping_postal_code' => '1000',
            'payment_method' => 'cod',
        ];

        $cart = app(CartService::class);
        $cart->add($variant);
        $firstOrder = app(OrderService::class)->place($customer);

        $cart->add($variant->fresh());
        $secondOrder = app(OrderService::class)->place($customer);

        $prefix = 'SY-'.now()->format('ymd').'-';
        $this->assertSame($prefix.'0001', $firstOrder->order_number);
        $this->assertSame($prefix.'0002', $secondOrder->order_number);
        $this->assertDatabaseHas('order_number_sequences', [
            'order_date' => now()->toDateString(),
            'last_number' => 2,
        ]);
    }

    public function test_cart_rejects_quantity_above_available_stock(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create(['stock_quantity' => 1]);

        $this->expectException(ValidationException::class);
        app(CartService::class)->add($variant, 2);
    }

    public function test_shipping_fee_applies_below_free_shipping_threshold(): void
    {
        config()->set('sneakyard.shipping_fee', 15000);
        config()->set('sneakyard.free_shipping_threshold', 300000);
        $product = Product::factory()->create(['price' => 199500]);
        $variant = ProductVariant::factory()->for($product)->create(['stock_quantity' => 3]);
        app(CartService::class)->add($variant);

        $this->assertSame(15000, app(CartService::class)->shippingTotal());
        $this->assertSame(214500, app(CartService::class)->total());
    }
}
