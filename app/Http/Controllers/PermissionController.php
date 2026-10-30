<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class PermissionController extends Controller
{
    /** Route name: permissions */
    public function index(Request $request): View
    {
        $roles = Role::orderBy('id')->get();

        $role = $roles->firstWhere('slug', $request->query('role'))
            ?? $roles->first(fn (Role $r) => ! $r->isAdmin())
            ?? $roles->first();

        abort_if(! $role, 404, 'No roles found.');

        $modules = config('permissions.modules');

        // Column headings = every action used anywhere, in config order.
        $actions = collect($modules)->flatMap(fn ($m) => $m['actions'])->unique()->values();

        return view('permissions.index', [
            'roles'         => $roles,
            'role'          => $role,
            'modules'       => $modules,
            'actions'       => $actions,
            'permissionIds' => Permission::pluck('id', 'slug'),          // slug => id
            'granted'       => $role->permissions()->pluck('permissions.id')->all(),
        ]);
    }

    /** Route name: permissions.update */
    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->isAdmin(), 403, 'The administrator role cannot be modified.');

        $data = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('permissions', ['role' => $role->slug])
            ->with('success', "Permissions updated for {$role->name}.");
    }
}