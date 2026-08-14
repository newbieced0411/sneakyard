<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AdminPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'permissions', 'is_system'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function grants(AdminPermission|string $permission): bool
    {
        $value = $permission instanceof AdminPermission ? $permission->value : $permission;

        return $this->slug === 'admin' || in_array($value, $this->permissions ?? [], true);
    }

    public function isProtected(): bool
    {
        return in_array($this->slug, ['admin', 'customer'], true);
    }
}
