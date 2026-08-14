<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id', 'order_number', 'user_id', 'status', 'payment_status', 'payment_method',
        'customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'shipping_city',
        'shipping_province', 'shipping_postal_code', 'subtotal', 'shipping_total', 'discount_total',
        'grand_total', 'customer_notes', 'admin_notes', 'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'integer',
            'shipping_total' => 'integer',
            'discount_total' => 'integer',
            'grand_total' => 'integer',
            'placed_at' => 'immutable_datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₱'.number_format($this->grand_total / 100, 2);
    }
}
