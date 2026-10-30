<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Safe to re-run: syncs config/permissions.php into the database.
     *   php artisan db:seed --class=PermissionSeeder
     */
    public function run(): void
    {
        $modules = config('permissions.modules');
        $known = [];

        foreach ($modules as $module => $definition) {
            foreach ($definition['actions'] as $action) {
                $slug = "{$module}.{$action}";
                $known[] = $slug;

                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'module' => $module,
                        'name'   => Str::headline($action) . ' ' . $definition['label'],
                    ]
                );
            }
        }

        // Drop permissions that were removed from the config (pivot rows cascade).
        Permission::whereNotIn('slug', $known)->delete();

        // Starting grants for roles that have none yet.
        $all = Permission::all(['id', 'slug']);

        foreach (config('permissions.defaults', []) as $roleSlug => $patterns) {
            $role = Role::where('slug', $roleSlug)->first();

            if (! $role || $role->permissions()->exists()) {
                continue;
            }

            $ids = $all
                ->filter(fn ($p) => collect($patterns)->contains(fn ($pattern) => Str::is($pattern, $p->slug)))
                ->pluck('id');

            $role->permissions()->sync($ids);
        }

        Role::all()->each->flushPermissionCache();
    }
}
