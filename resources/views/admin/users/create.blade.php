<x-layouts.admin title="Add user">
    <div class="admin-page-heading"><div><p class="eyebrow">Access management</p><h1>Add user</h1><p>Create a staff account and choose its access role.</p></div><flux:button variant="ghost" icon="arrow-left" href="{{ route('admin.users.index') }}">All users</flux:button></div>
    <form class="admin-form" method="POST" action="{{ route('admin.users.store') }}">@csrf
        @include('admin.users.form', ['managedUser' => null])
    </form>
</x-layouts.admin>
