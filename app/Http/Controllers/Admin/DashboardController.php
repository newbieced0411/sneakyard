<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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
        $stats = [
            'pending_orders' => Order::query()->where('status', OrderStatus::Pending)->count(),
            'active_products' => Product::query()->where('status', ProductStatus::Active)->count(),
            'low_stock' => Product::query()->whereHas('variants', fn ($query) => $query->where('stock_quantity', '<=', 3))->count(),
            'revenue' => Order::query()->whereNot('status', OrderStatus::Cancelled)->sum('grand_total'),
        ];

        $orders = Order::query()->withCount('items')->latest('placed_at')->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'orders'));
    }
}
