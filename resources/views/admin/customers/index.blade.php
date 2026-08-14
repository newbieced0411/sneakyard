<x-layouts.admin title="Customers">
    <div class="admin-page-heading"><div><p class="eyebrow">Customer records</p><h1>Customers</h1><p>Guest checkouts are automatically grouped into a single customer history.</p></div></div>
    <form class="admin-filters" method="GET">
        <input class="admin-input" type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, or phone" aria-label="Search customers">
        <flux:button type="submit">Search</flux:button>
    </form>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Customer</th><th>Phone</th><th>Orders</th><th>Lifetime value</th><th>Last updated</th></tr></thead>
        <tbody>
            @forelse($customers as $customer)
                <tr><td><a href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a><small class="admin-table-secondary">{{ $customer->email }}</small></td><td>{{ $customer->phone ?: '—' }}</td><td>{{ $customer->orders_count }}</td><td>{{ $customer->formatted_lifetime_value }}</td><td>{{ $customer->updated_at->diffForHumans() }}</td></tr>
            @empty<tr><td colspan="5">No customers match your search.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $customers->links() }}</div>
</x-layouts.admin>
