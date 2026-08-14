<?php

declare(strict_types=1);

use App\Enums\AdminPermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->json('permissions')->default('[]');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        $now = now();
        DB::table('roles')->insert([
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full access to every Sneakyard administration feature.',
                'permissions' => json_encode(array_column(AdminPermission::cases(), 'value')),
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Store manager',
                'slug' => 'manager',
                'description' => 'Manages products, orders, and customer service.',
                'permissions' => json_encode([
                    AdminPermission::AccessAdmin->value,
                    AdminPermission::ManageCatalog->value,
                    AdminPermission::ManageOrders->value,
                    AdminPermission::ManageCustomers->value,
                ]),
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fulfillment',
                'slug' => 'fulfillment',
                'description' => 'Processes orders and reviews delivery information.',
                'permissions' => json_encode([
                    AdminPermission::AccessAdmin->value,
                    AdminPermission::ManageOrders->value,
                    AdminPermission::ManageCustomers->value,
                ]),
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Storefront customer account without admin access.',
                'permissions' => json_encode([]),
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('password')->index();
        });

        $roleIds = DB::table('roles')->pluck('id', 'slug');
        DB::table('users')->where('role', 'admin')->update(['role_id' => $roleIds['admin']]);
        DB::table('users')->where('role', '!=', 'admin')->update(['role_id' => $roleIds['customer']]);

        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 40)->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_province', 100)->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        DB::table('orders')->orderBy('id')->chunkById(100, function ($orders): void {
            foreach ($orders as $order) {
                $email = Str::lower(trim((string) $order->customer_email));
                $customerId = DB::table('customers')->where('email', $email)->value('id');
                $attributes = [
                    'user_id' => $order->user_id,
                    'name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'shipping_address' => $order->shipping_address,
                    'shipping_city' => $order->shipping_city,
                    'shipping_province' => $order->shipping_province,
                    'shipping_postal_code' => $order->shipping_postal_code,
                    'updated_at' => now(),
                ];

                if ($customerId === null) {
                    $customerId = DB::table('customers')->insertGetId([
                        ...$attributes,
                        'email' => $email,
                        'created_at' => now(),
                    ]);
                } else {
                    DB::table('customers')->where('id', $customerId)->update($attributes);
                }

                DB::table('orders')->where('id', $order->id)->update(['customer_id' => $customerId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('customer_id');
        });
        Schema::dropIfExists('customers');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn('is_active');
        });
        Schema::dropIfExists('roles');
    }
};
