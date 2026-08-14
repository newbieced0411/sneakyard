<?php

declare(strict_types=1);

namespace App\Enums;

enum AdminPermission: string
{
    case AccessAdmin = 'access-admin';
    case ManageCatalog = 'manage-catalog';
    case ManageOrders = 'manage-orders';
    case ManageCustomers = 'manage-customers';
    case ManageUsers = 'manage-users';
    case ManageRoles = 'manage-roles';

    public function label(): string
    {
        return match ($this) {
            self::AccessAdmin => 'Access admin',
            self::ManageCatalog => 'Manage catalog',
            self::ManageOrders => 'Manage orders',
            self::ManageCustomers => 'Manage customers',
            self::ManageUsers => 'Manage users',
            self::ManageRoles => 'Manage roles',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::AccessAdmin => 'Sign in to the administration workspace.',
            self::ManageCatalog => 'Create and update products, pricing, and inventory.',
            self::ManageOrders => 'Review orders and update fulfillment or payment status.',
            self::ManageCustomers => 'View customer history and maintain internal notes.',
            self::ManageUsers => 'Create staff accounts and change their access role.',
            self::ManageRoles => 'Create roles and configure permission sets.',
        };
    }
}
