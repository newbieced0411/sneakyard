<x-layouts.admin title="Dashboard">
    <div class="admin-page-heading"><div><h1>Dashboard</h1><p>Live view of orders, inventory, and store performance.</p></div><flux:button variant="primary" icon="plus" href="{{ route('admin.products.create') }}">Add product</flux:button></div>
    <section class="admin-stats" aria-label="Store metrics">
        <article class="stat-card"><span>Pending orders</span><strong>{{ number_format($stats['pending_orders']) }}</strong></article>
        <article class="stat-card"><span>Active products</span><strong>{{ number_format($stats['active_products']) }}</strong></article>
        <article class="stat-card"><span>Low-stock products</span><strong>{{ number_format($stats['low_stock']) }}</strong></article>
        <article class="stat-card"><span>Gross sales</span><strong>₱{{ number_format($stats['revenue'] / 100, 2) }}</strong></article>
    </section>
    <section><div class="admin-page-heading"><div><h2>Recent orders</h2></div><flux:button variant="ghost" href="{{ route('admin.orders.index') }}" icon:trailing="arrow-right">View all</flux:button></div>
        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Items</th><th>Total</th><th>Placed</th></tr></thead><tbody>
            @forelse($orders as $order)<tr><td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td><td>{{ $order->customer_name }}</td><td><span class="status-badge {{ $order->status->value }}">{{ $order->status->label() }}</span></td><td>{{ $order->items_count }}</td><td>{{ $order->formatted_total }}</td><td>{{ $order->placed_at?->diffForHumans() }}</td></tr>@empty<tr><td colspan="6">No orders yet. The first one will appear here in real time.</td></tr>@endforelse
        </tbody></table></div>
    </section>
</x-layouts.admin>
