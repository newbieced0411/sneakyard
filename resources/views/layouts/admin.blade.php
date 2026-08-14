@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111111">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|playfair-display:500,600|special-elite:400" rel="stylesheet">
    <title>{{ $title }} | Sneakyard Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
</head>
<body class="admin-body" data-admin="true">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="wordmark" href="{{ route('admin.dashboard') }}" aria-label="Sneakyard admin home"><x-brand-logo /></a>
            <nav class="admin-nav" aria-label="Admin navigation">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><flux:icon.home class="size-5" /><span>Dashboard</span></a>
                @can('manage-catalog')
                <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><flux:icon.rectangle-group class="size-5" /><span>Products</span></a>
                @endcan
                @can('manage-orders')
                <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><flux:icon.shopping-bag class="size-5" /><span>Orders</span></a>
                @endcan
                @can('manage-customers')
                <a class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><flux:icon.user-group class="size-5" /><span>Customers</span></a>
                @endcan
                @can('manage-users')
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><flux:icon.users class="size-5" /><span>Users</span></a>
                @endcan
                @can('manage-roles')
                <a class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"><flux:icon.shield-check class="size-5" /><span>Roles</span></a>
                @endcan
            </nav>
            <div class="admin-sidebar-footer"><a href="{{ route('home') }}"><flux:icon.arrow-top-right-on-square class="size-5" /><span>View store</span></a></div>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <button type="button" class="secondary-button" onclick="window.dispatchEvent(new Event('sneakyard:enable-notifications'))">Enable alerts</button>
                <livewire:admin.notification-center />
                <flux:dropdown position="bottom" align="end">
                    <flux:button variant="ghost" icon:trailing="chevron-down">{{ auth()->user()->name }}</flux:button>
                    <flux:menu>
                        <flux:menu.item href="{{ route('admin.profile.edit') }}" icon="user-circle">Profile settings</flux:menu.item>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('admin.logout') }}">@csrf<flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">Sign out</flux:menu.item></form>
                    </flux:menu>
                </flux:dropdown>
            </header>
            <main class="admin-content">
                @if(session('success'))<div class="admin-flash" role="status">{{ session('success') }}</div>@endif
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
    @fluxScripts
</body>
</html>
