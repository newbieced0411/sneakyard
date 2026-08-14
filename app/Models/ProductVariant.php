<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'sku', 'size', 'color', 'stock_quantity', 'reserved_quantity', 'is_active'];

    protected function casts(): array
    {
        return ['stock_quantity' => 'integer', 'reserved_quantity' => 'integer', 'is_active' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereColumn('stock_quantity', '>', 'reserved_quantity');
    }

    public function availableQuantity(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }
}
