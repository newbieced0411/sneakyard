<x-layouts.admin title="Users">
    <div class="admin-page-heading">
        <div><p class="eyebrow">Access management</p><h1>Users</h1><p>Create staff accounts, assign roles, and control access.</p></div>
        <flux:button variant="primary" icon="user-plus" href="{{ route('admin.users.create') }}">Add user</flux:button>
    </div>
    <form class="admin-filters" method="GET">
        <input class="admin-input" type="search" name="search" value="{{ request('search') }}" placeholder="Search name or email" aria-label="Search staff users">
        <select class="admin-select" name="role" aria-label="Filter by role">
            <option value="">All roles</option>
            @foreach($roles as $role)<option value="{{ $role->slug }}" @selected(request('role') === $role->slug)>{{ $role->name }}</option>@endforeach
        </select>
        <flux:button type="submit">Filter</flux:button>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Last updated</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><div class="admin-identity"><span class="admin-avatar" aria-hidden="true">{{ str($user->name)->substr(0, 1)->upper() }}</span><span><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></span></div></td>
                        <td>{{ $user->accessRole?->name ?? ucfirst($user->role) }}</td>
                        <td><span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>{{ $user->updated_at->diffForHumans() }}</td>
                        <td><flux:button size="sm" variant="ghost" href="{{ route('admin.users.edit', $user) }}">Edit</flux:button></td>
                    </tr>
                @empty
                    <tr><td colspan="5">No staff users match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $users->links() }}</div>
</x-layouts.admin>
