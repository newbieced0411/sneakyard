<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Livewire\Component;

final class NotificationCenter extends Component
{
    public bool $open = false;

    public function markAllRead(): void
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render(): View
    {
        $user = auth()->user();

        return view('livewire.admin.notification-center', [
            'notifications' => $user?->notifications()->latest()->limit(8)->get() ?? collect(),
            'unreadCount' => $user?->unreadNotifications()->count() ?? 0,
        ]);
    }
}
