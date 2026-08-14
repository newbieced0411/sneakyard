<x-layouts.admin title="Roles">
    <div class="admin-page-heading">
        <div><p class="eyebrow">Access management</p><h1>Roles</h1><p>Define exactly what each staff group can see and manage.</p></div>
        <flux:button variant="primary" icon="plus" href="{{ route('admin.roles.create') }}">Add role</flux:button>
    </div>
    @error('role')<div class="form-alert" role="alert">{{ $message }}</div>@enderror
    <div class="role-grid">
        @foreach($roles as $role)
            @php($grantedPermissions = collect($permissions)->filter(fn ($permission) => $role->grants($permission)))
            <article class="role-card">
                <div class="role-card-heading"><div><h2>{{ $role->name }}</h2><p>{{ $role->description ?: 'Custom staff access role.' }}</p></div>@if($role->is_system)<span class="status-badge">System</span>@endif</div>
                <p class="role-user-count">{{ trans_choice(':count user|:count users', $role->users_count, ['count' => $role->users_count]) }}</p>
                <ul class="permission-summary">
                    @forelse($grantedPermissions as $permission)<li><flux:icon.check class="size-4" />{{ $permission->label() }}</li>@empty<li>No admin permissions</li>@endforelse
                </ul>
                <div class="role-card-actions">
                    @if(!$role->isProtected())<flux:button size="sm" variant="ghost" href="{{ route('admin.roles.edit', $role) }}">Edit role</flux:button>@else<span class="role-locked"><flux:icon.lock-closed class="size-4" />Protected</span>@endif
                </div>
            </article>
        @endforeach
    </div>
</x-layouts.admin>
