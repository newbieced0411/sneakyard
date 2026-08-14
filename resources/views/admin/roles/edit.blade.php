<x-layouts.admin :title="'Edit '.$role->name">
    <div class="admin-page-heading"><div><p class="eyebrow">Access management</p><h1>Edit role</h1><p>Permission changes apply to every user assigned to this role.</p></div><flux:button variant="ghost" icon="arrow-left" href="{{ route('admin.roles.index') }}">All roles</flux:button></div>
    <form class="admin-form" method="POST" action="{{ route('admin.roles.update', $role) }}">@csrf @method('PUT')
        @include('admin.roles.form')
    </form>
    @if(!$role->is_system)
        <section class="admin-card admin-danger-zone"><div><h2>Delete role</h2><p>Only empty roles can be deleted.</p></div><form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">@csrf @method('DELETE')<flux:button variant="danger" type="submit">Delete role</flux:button></form></section>
    @endif
</x-layouts.admin>
