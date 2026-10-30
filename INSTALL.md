# Role-based permissions: install steps

Copy the files into your Laravel project at the same paths, then:

1. **Merge, don't overwrite**, these three if they already exist:
   - `app/Models/Role.php`: keep your fields, add the constant, relationships and permission methods
   - `app/Models/User.php`: add `use \App\Models\Concerns\HasRolePermissions;` and remove any existing `role()` method
   - `app/Providers/AppServiceProvider.php`: add the `Gate::before` block to `boot()`
2. Run the migration and seeder:
   ```
   php artisan migrate
   php artisan db:seed --class=PermissionSeeder
   ```
3. Paste the routes from `routes/web.snippet.php` into `routes/web.php`.
4. In each resource controller:
   ```php
   use App\Http\Controllers\Concerns\AuthorizesCrud;
   use Illuminate\Routing\Controllers\HasMiddleware;

   class TeacherController extends Controller implements HasMiddleware
   {
       use AuthorizesCrud;
       protected static string $permissionModule = 'teachers';
   }
   ```
5. Views: the matrix page extends `layouts.app` and uses `@push('scripts')`. Adjust both names to match your layout.
6. Clear caches: `php artisan optimize:clear`.

## Day to day

| Task | Where |
|---|---|
| Add a module or action | `config/permissions.php`, then re-run the seeder |
| Add a menu item | `config/menu.php` |
| Change what a role can do | Permissions page (matrix) |
| Hide a button | `@can('teachers.create') ... @endcan` |
| Protect a single route | `->middleware('can:payments.update')` |
| Check in a controller | `$this->authorize('teachers.export')` |

## Notes

- Hiding menu items is cosmetic; the `can:` middleware is the real protection.
- Whoever holds `permissions.update` can grant themselves anything. Keep it admin-only.
- Parent/teacher "own records only" rules belong in Policies. `Gate::before` returns `null` (never `false`) so policies still run.
- Role permissions are cached per role and flushed automatically when a role's permissions change.
- If you add a role by hand in the DB, add its slug under `defaults` in `config/permissions.php` (or just tick it in the matrix).
