<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Jobs\SyncProductToMeta;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_require_an_admin_account(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));

        $customer = User::factory()->create();
        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_sign_in(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@sneakyard.ph', 'password' => 'secret-password']);

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'secret-password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_create_product_with_inventory(): void
    {
        Storage::fake('public');
        Queue::fake();
        $admin = User::factory()->admin()->create();
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'New Verified Pair',
            'sku' => 'SY-NEW-PAIR',
            'description' => 'A fully verified premium sneaker ready for the Sneakyard collection.',
            'price' => '7495.00',
            'gender' => 'unisex',
            'status' => 'active',
            'is_featured' => '1',
            'image' => UploadedFile::fake()->create('pair.png', 100, 'image/png'),
            'variants' => [['sku' => 'SY-NEW-PAIR-US9', 'size' => '9', 'color' => 'Bone', 'stock_quantity' => 6]],
        ]);

        $product = Product::query()->where('sku', 'SY-NEW-PAIR')->firstOrFail();
        $response->assertRedirect(route('admin.products.edit', $product));
        $this->assertSame(749500, $product->price);
        $this->assertDatabaseHas('product_variants', ['product_id' => $product->id, 'size' => '9', 'stock_quantity' => 6]);
        Queue::assertPushed(SyncProductToMeta::class);
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();

        $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => OrderStatus::Processing->value,
            'payment_status' => PaymentStatus::Paid->value,
            'admin_notes' => 'Payment verified.',
        ])->assertRedirect();

        $this->assertSame(OrderStatus::Processing, $order->fresh()->status);
        $this->assertSame(PaymentStatus::Paid, $order->fresh()->payment_status);
    }
}
