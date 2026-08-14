@php($isSelf = $managedUser?->is(auth()->user()) ?? false)
<section class="admin-form-section">
    <div class="admin-card-heading"><div><h2>Account details</h2><p>Use a business email the staff member can access.</p></div></div>
    <div class="admin-form-grid">
        <label class="admin-field"><span>Full name</span><input class="admin-input" name="name" value="{{ old('name', $managedUser?->name) }}" autocomplete="name" required>@error('name')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label class="admin-field"><span>Email address</span><input class="admin-input" type="email" name="email" value="{{ old('email', $managedUser?->email) }}" autocomplete="email" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label class="admin-field"><span>Role</span><select class="admin-select" name="role_id" required @disabled($isSelf)>@foreach($roles as $role)<option value="{{ $role->id }}" @selected((string) old('role_id', $managedUser?->role_id) === (string) $role->id)>{{ $role->name }}</option>@endforeach</select>@if($isSelf)<input type="hidden" name="role_id" value="{{ $managedUser->role_id }}"><small>Change your own role through another administrator.</small>@endif @error('role_id')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label class="admin-field"><span>{{ $managedUser ? 'New password (optional)' : 'Password' }}</span><input class="admin-input" type="password" name="password" autocomplete="new-password" @required(!$managedUser)>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label class="admin-field"><span>Confirm password</span><input class="admin-input" type="password" name="password_confirmation" autocomplete="new-password" @required(!$managedUser)></label>
        <div class="admin-field"><span>Account status</span>
            @if($isSelf)
                <input type="hidden" name="is_active" value="1"><label class="admin-checkbox"><input type="checkbox" checked disabled><span>Active</span></label><small>You cannot deactivate your own account.</small>
            @else
                <input type="hidden" name="is_active" value="0"><label class="admin-checkbox"><input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $managedUser?->is_active ?? true))><span>Allow this user to sign in</span></label>
            @endif
            @error('is_active')<small class="field-error">{{ $message }}</small>@enderror
        </div>
    </div>
</section>
<div class="admin-actions"><flux:button variant="ghost" href="{{ route('admin.users.index') }}">Cancel</flux:button><flux:button variant="primary" type="submit">{{ $managedUser ? 'Save user' : 'Create user' }}</flux:button></div>
