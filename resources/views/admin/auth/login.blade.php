<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192.png') }}"><link rel="preconnect" href="https://fonts.bunny.net"><link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|playfair-display:500,600|special-elite:400" rel="stylesheet"><title>Admin sign in | Sneakyard</title>@vite(['resources/css/app.css', 'resources/js/app.js']) @fluxAppearance</head>
<body class="admin-login-page">
    <div class="admin-login-image" aria-hidden="true"></div>
    <main class="admin-login-panel"><div class="admin-login-card"><a class="wordmark" href="{{ route('home') }}" aria-label="Sneakyard home"><x-brand-logo /></a><h1>Welcome back.</h1><p>Sign in to manage products, inventory, and orders.</p>
        <form method="POST" action="{{ route('admin.login.store') }}" novalidate>@csrf
            <label class="admin-field"><span>Email address</span><input class="admin-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="admin-field"><span>Password</span><input class="admin-input" type="password" name="password" autocomplete="current-password" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="admin-checkbox"><input type="checkbox" name="remember" value="1"><span>Keep me signed in</span></label>
            <flux:button variant="primary" type="submit" class="w-full">Sign in</flux:button>
        </form>
    </div></main>
    @fluxScripts
</body></html>
