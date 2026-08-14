<x-layouts.admin title="Add role">
    <div class="admin-page-heading"><div><p class="eyebrow">Access management</p><h1>Add role</h1><p>Bundle the permissions needed for a specific job.</p></div><flux:button variant="ghost" icon="arrow-left" href="{{ route('admin.roles.index') }}">All roles</flux:button></div>
    <form class="admin-form" method="POST" action="{{ route('admin.roles.store') }}">@csrf
        @include('admin.roles.form', ['role' => null])
    </form>
</x-layouts.admin>
