<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AdminPermission;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminAccessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_staff_user_cannot_sign_in(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'inactive@sneakyard.ph',
            'password' => 'SecurePassword1',
            'is_active' => false,
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'SecurePassword1',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_role_permissions_limit_admin_modules(): void
    {
        $managerRole = Role::query()->where('slug', 'manager')->firstOrFail();
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'role' => 'manager',
        ]);

        $this->actingAs($manager)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($manager)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($manager)->get(route('admin.customers.index'))->assertOk();
        $this->actingAs($manager)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($manager)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_administrator_can_create_staff_and_cannot_lock_own_account(): void
    {
        $adminRole = Role::query()->where('slug', 'admin')->firstOrFail();
        $fulfillmentRole = Role::query()->where('slug', 'fulfillment')->firstOrFail();
        $admin = User::factory()->admin()->create(['role_id' => $adminRole->id]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Order Team',
            'email' => 'orders@sneakyard.ph',
            'role_id' => $fulfillmentRole->id,
            'password' => 'SecurePassword1',
            'password_confirmation' => 'SecurePassword1',
            'is_active' => true,
        ]);

        $staff = User::query()->where('email', 'orders@sneakyard.ph')->firstOrFail();
        $response->assertRedirect(route('admin.users.edit', $staff));
        $this->assertSame($fulfillmentRole->id, $staff->role_id);
        $this->assertTrue($staff->is_active);

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => $fulfillmentRole->id,
            'password' => null,
            'password_confirmation' => null,
            'is_active' => false,
        ])->assertRedirect();

        $admin->refresh();
        $this->assertSame($adminRole->id, $admin->role_id);
        $this->assertTrue($admin->is_active);
    }

    public function test_administrator_can_create_custom_role_and_system_roles_are_protected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name' => 'Customer care',
            'description' => 'Supports customers and reviews their orders.',
            'permissions' => [
                AdminPermission::AccessAdmin->value,
                AdminPermission::ManageOrders->value,
                AdminPermission::ManageCustomers->value,
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('roles', ['slug' => 'customer-care']);

        $administratorRole = Role::query()->where('slug', 'admin')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.roles.edit', $administratorRole))->assertForbidden();
    }

    public function test_customer_module_shows_history_and_saves_private_notes(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = Customer::query()->create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'phone' => '09171234567',
        ]);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
        ]);

        $this->actingAs($admin)->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee($customer->email)
            ->assertSee($order->order_number);

        $this->actingAs($admin)->put(route('admin.customers.update', $customer), [
            'admin_notes' => 'Prefers size 9 low-top releases.',
        ])->assertRedirect();

        $this->assertSame('Prefers size 9 low-top releases.', $customer->fresh()->admin_notes);
    }

    public function test_staff_user_can_update_profile_and_password(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'OldPassword1']);

        $this->actingAs($admin)->put(route('admin.profile.update'), [
            'name' => 'Sneakyard Owner',
            'email' => 'owner@sneakyard.ph',
        ])->assertRedirect();

        $this->actingAs($admin)->put(route('admin.profile.password'), [
            'current_password' => 'OldPassword1',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ])->assertRedirect();

        $admin->refresh();
        $this->assertSame('Sneakyard Owner', $admin->name);
        $this->assertSame('owner@sneakyard.ph', $admin->email);
        $this->assertTrue(Hash::check('NewPassword2', $admin->password));
    }
}
