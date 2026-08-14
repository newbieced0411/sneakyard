<x-layouts.admin title="Profile settings">
    <div class="admin-page-heading"><div><p class="eyebrow">Your account</p><h1>Profile settings</h1><p>Keep your account information and password up to date.</p></div></div>
    <div class="profile-grid">
        <form class="admin-form-section admin-form" method="POST" action="{{ route('admin.profile.update') }}">@csrf @method('PUT')
            <div class="admin-card-heading"><div><h2>Personal information</h2><p>This name appears throughout the admin workspace.</p></div></div>
            <label class="admin-field"><span>Full name</span><input class="admin-input" name="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" required>@error('name')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="admin-field"><span>Email address</span><input class="admin-input" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
            <flux:button variant="primary" type="submit">Save profile</flux:button>
        </form>
        <form class="admin-form-section admin-form" method="POST" action="{{ route('admin.profile.password') }}">@csrf @method('PUT')
            <div class="admin-card-heading"><div><h2>Change password</h2><p>Use at least 10 characters with uppercase, lowercase, and a number.</p></div></div>
            <label class="admin-field"><span>Current password</span><input class="admin-input" type="password" name="current_password" autocomplete="current-password" required>@error('current_password')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="admin-field"><span>New password</span><input class="admin-input" type="password" name="password" autocomplete="new-password" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="admin-field"><span>Confirm new password</span><input class="admin-input" type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <flux:button variant="primary" type="submit">Update password</flux:button>
        </form>
    </div>
</x-layouts.admin>
