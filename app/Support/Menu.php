<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Turns config/menu.php into the tree this user is allowed to see,
 * with resolved URLs, active/open flags and badges.
 */
class Menu
{
    public static function for(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return self::filter(config('menu', []), $user);
    }

    protected static function filter(array $nodes, User $user): array
    {
        $visible = [];

        foreach ($nodes as $node) {
            if (! empty($node['children'])) {
                $node['children'] = self::filter($node['children'], $user);

                // A section with nothing visible inside it disappears.
                if (empty($node['children'])) {
                    continue;
                }

                $node['active'] = collect($node['children'])->contains('active', true);
            } else {
                if (! empty($node['permission']) && ! $user->can($node['permission'])) {
                    continue;
                }

                $node['active'] = request()->routeIs(...self::activePatterns($node));
            }

            // Routes that don't exist yet render as disabled "Coming soon" links.
            $node['url'] = isset($node['route']) && Route::has($node['route'])
                ? route($node['route'])
                : null;

            $node['badge'] = isset($node['badge']) ? self::badge($node['badge']) : null;

            $visible[] = $node;
        }

        return $visible;
    }

    protected static function activePatterns(array $node): array
    {
        if (! empty($node['active'])) {
            return (array) $node['active'];
        }

        if (empty($node['route'])) {
            return [];
        }

        // teachers.index -> teachers.*   |   permissions -> permissions, permissions.*
        $prefix = str_contains($node['route'], '.')
            ? Str::beforeLast($node['route'], '.')
            : $node['route'];

        return [$prefix, "{$prefix}.*"];
    }

    protected static function badge(string $key): ?string
    {
        return match ($key) {
            'users' => (string) Cache::remember('menu.badge.users', 300, fn () => User::count()),
            default => null,
        };
    }
}
