<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(private readonly Order $order)
    {
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        if (config('sneakyard.notifications.email_enabled')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Sneakyard order {$this->order->order_number}")
            ->greeting('A new order just arrived.')
            ->line("{$this->order->customer_name} placed an order worth {$this->order->formatted_total}.")
            ->action('Review order', route('admin.orders.show', $this->order));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->customer_name,
            'grand_total' => $this->order->grand_total,
            'message' => "New order {$this->order->order_number} from {$this->order->customer_name}",
            'url' => route('admin.orders.show', $this->order),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
