<section class="admin-form-section">
    <div class="admin-card-heading"><div><h2>Role details</h2><p>Choose a clear name staff members will recognize.</p></div></div>
    <div class="admin-form-grid">
        <label class="admin-field"><span>Role name</span><input class="admin-input" name="name" value="{{ old('name', $role?->name) }}" required>@error('name')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label class="admin-field full-span"><span>Description</span><textarea class="admin-textarea" name="description" rows="3">{{ old('description', $role?->description) }}</textarea>@error('description')<small class="field-error">{{ $message }}</small>@enderror</label>
    </div>
</section>
<section class="admin-form-section">
    <div class="admin-card-heading"><div><h2>Permissions</h2><p>Select only the tools this role needs. Admin access is required.</p></div></div>
    @php($selectedPermissions = old('permissions', $role?->permissions ?? [\App\Enums\AdminPermission::AccessAdmin->value]))
    <div class="permission-grid">
        @foreach($permissions as $permission)
            <label class="permission-option"><input type="checkbox" name="permissions[]" value="{{ $permission->value }}" @checked(in_array($permission->value, $selectedPermissions, true))><span><strong>{{ $permission->label() }}</strong><small>{{ $permission->description() }}</small></span></label>
        @endforeach
    </div>
    @error('permissions')<p class="field-error">{{ $message }}</p>@enderror
    @error('permissions.*')<p class="field-error">{{ $message }}</p>@enderror
</section>
<div class="admin-actions"><flux:button variant="ghost" href="{{ route('admin.roles.index') }}">Cancel</flux:button><flux:button variant="primary" type="submit">{{ $role ? 'Save role' : 'Create role' }}</flux:button></div>
