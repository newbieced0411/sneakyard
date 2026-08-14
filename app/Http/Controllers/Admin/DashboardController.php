<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AdminPermission;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $canManageOrders = request()->user()->hasPermission(AdminPermission::ManageOrders);
        $canManageCatalog = request()->user()->hasPermission(AdminPermission::ManageCatalog);

        $stats = [
            'pending_orders' => $canManageOrders ? Order::query()->where('status', OrderStatus::Pending)->count() : null,
            'active_products' => $canManageCatalog ? Product::query()->where('status', ProductStatus::Active)->count() : null,
            'low_stock' => $canManageCatalog ? Product::query()->whereHas('variants', fn ($query) => $query->where('stock_quantity', '<=', 3))->count() : null,
            'revenue' => $canManageOrders ? Order::query()->whereNot('status', OrderStatus::Cancelled)->sum('grand_total') : null,
        ];

        $orders = $canManageOrders
            ? Order::query()->withCount('items')->latest('placed_at')->limit(8)->get()
            : collect();

        return view('admin.dashboard', compact('stats', 'orders', 'canManageCatalog', 'canManageOrders'));
    }
}
