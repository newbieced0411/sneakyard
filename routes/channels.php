<?php

use App\Enums\AdminPermission;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin.orders', fn ($user): bool => $user->is_active && $user->hasPermission(AdminPermission::ManageOrders));
