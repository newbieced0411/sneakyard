<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seed_creates_admin_without_demo_catalog(): void
    {
        config()->set([
            'sneakyard.admin.email' => 'owner@sneakyard.ph',
            'sneakyard.admin.password' => 'production-test-password',
            'sneakyard.seed_demo_catalog' => false,
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'owner@sneakyard.ph')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertSame('admin', $admin->accessRole->slug);
        $this->assertTrue(Hash::check('production-test-password', $admin->password));
        $this->assertSame(0, Product::query()->count());
    }
}
