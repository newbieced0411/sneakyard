<x-layouts.admin :title="'Edit '.$managedUser->name">
    <div class="admin-page-heading"><div><p class="eyebrow">Access management</p><h1>Edit user</h1><p>Update account details, role, password, or access status.</p></div><flux:button variant="ghost" icon="arrow-left" href="{{ route('admin.users.index') }}">All users</flux:button></div>
    <form class="admin-form" method="POST" action="{{ route('admin.users.update', $managedUser) }}">@csrf @method('PUT')
        @include('admin.users.form')
    </form>
</x-layouts.admin>
