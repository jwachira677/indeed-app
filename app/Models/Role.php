<?php

// If you already have app/Models/Role.php, merge the constant, the relationships
// and the permission methods into it instead of replacing the file.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    public const ADMIN = 'admin';

    protected $fillable = ['name', 'slug', 'description', 'is_default'];

    /** Per-request memo so a page with 30 @can checks hits the cache once. */
    protected ?array $permissionSlugsMemo = null;

    protected static function booted(): void
    {
        static::saved(fn (Role $role) => $role->flushPermissionCache());
        static::deleted(fn (Role $role) => $role->flushPermissionCache());
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function isAdmin(): bool
    {
        return $this->slug === self::ADMIN;
    }

    /** @return string[] */
    public function permissionSlugs(): array
    {
        return $this->permissionSlugsMemo ??= Cache::rememberForever(
            $this->permissionCacheKey(),
            fn () => $this->permissions()->pluck('permissions.slug')->all()
        );
    }

    public function syncPermissions(iterable $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
        $this->flushPermissionCache();
    }

    public function flushPermissionCache(): void
    {
        $this->permissionSlugsMemo = null;
        Cache::forget($this->permissionCacheKey());
    }

    protected function permissionCacheKey(): string
    {
        return "role.{$this->id}.permissions";
    }
}