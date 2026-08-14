<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AdminPermission;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['role_id', 'name', 'email', 'role', 'password', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function accessRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->whereHas('accessRole', fn (Builder $query): Builder => $query->where('slug', '!=', 'customer'));
    }

    public function isAdmin(): bool
    {
        return $this->accessRole?->slug === 'admin' || $this->role === 'admin';
    }

    public function hasPermission(AdminPermission|string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->accessRole?->grants($permission) ?? false;
    }

    public function canAccessAdmin(): bool
    {
        return $this->is_active && $this->hasPermission(AdminPermission::AccessAdmin);
    }
}
