<div class="notification-center" wire:poll.10s>
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="ghost" icon="bell" square aria-label="Notifications">
            @if($unreadCount)<span class="admin-notification-badge">{{ min($unreadCount, 99) }}</span>@endif
        </flux:button>
        <flux:menu class="notification-menu">
            <div class="notification-heading"><strong>Notifications</strong>@if($unreadCount)<button type="button" wire:click="markAllRead">Mark all read</button>@endif</div>
            @forelse($notifications as $notification)
                <a href="{{ data_get($notification->data, 'url', route('admin.dashboard')) }}" class="notification-item {{ $notification->read_at ? '' : 'unread' }}"><span>{{ data_get($notification->data, 'message', 'Order update') }}</span><small>{{ $notification->created_at->diffForHumans() }}</small></a>
            @empty
                <p class="notification-empty">No notifications yet.</p>
            @endforelse
        </flux:menu>
    </flux:dropdown>
</div>
