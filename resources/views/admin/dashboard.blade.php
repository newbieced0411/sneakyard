<x-layouts.admin title="Dashboard">
    <div class="admin-page-heading">
        <div>
            <h1>Dashboard</h1>
            <p>Live view of the parts of Sneakyard you manage.</p>
        </div>
        @if($canManageCatalog)
            <flux:button variant="primary" icon="plus" href="{{ route('admin.products.create') }}">Add product</flux:button>
        @endif
    </div>

    @if($canManageOrders || $canManageCatalog)
        <section class="admin-stats" aria-label="Store metrics">
            @if($canManageOrders)
                <article class="stat-card"><span>Pending orders</span><strong>{{ number_format($stats['pending_orders']) }}</strong></article>
                <article class="stat-card"><span>Gross sales</span><strong>₱{{ number_format($stats['revenue'] / 100, 2) }}</strong></article>
            @endif
            @if($canManageCatalog)
                <article class="stat-card"><span>Active products</span><strong>{{ number_format($stats['active_products']) }}</strong></article>
                <article class="stat-card"><span>Low-stock products</span><strong>{{ number_format($stats['low_stock']) }}</strong></article>
            @endif
        </section>
    @else
        <section class="admin-card admin-empty">
            <flux:icon.shield-check class="size-8" />
            <div><h2>Welcome to Sneakyard Admin</h2><p>Your account is active. Ask an administrator to assign the tools you need.</p></div>
        </section>
    @endif

    @if($canManageOrders)
        <section>
            <div class="admin-page-heading"><div><h2>Recent orders</h2></div><flux:button variant="ghost" href="{{ route('admin.orders.index') }}" icon:trailing="arrow-right">View all</flux:button></div>
            <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Items</th><th>Total</th><th>Placed</th></tr></thead><tbody>
                @forelse($orders as $order)<tr><td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td><td>{{ $order->customer_name }}</td><td><span class="status-badge {{ $order->status->value }}">{{ $order->status->label() }}</span></td><td>{{ $order->items_count }}</td><td>{{ $order->formatted_total }}</td><td>{{ $order->placed_at?->diffForHumans() }}</td></tr>@empty<tr><td colspan="6">No orders yet. The first one will appear here in real time.</td></tr>@endforelse
            </tbody></table></div>
        </section>
    @endif
</x-layouts.admin>
