<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        try {
            $query = Role::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Type filter
            if ($request->filled('type')) {
                $query->where('is_default', $request->input('type') === 'default');
            }

            // Sorting
            match ($request->input('sort', 'created_at_desc')) {
                'created_at_asc' => $query->oldest(),
                'name_asc'       => $query->orderBy('name', 'asc'),
                'name_desc'      => $query->orderBy('name', 'desc'),
                'slug_asc'       => $query->orderBy('slug', 'asc'),
                default          => $query->latest(),
            };

            $roles = $query->paginate(15)->withQueryString();

            return view('roles.index', compact('roles'));

        } catch (\Exception $e) {
            Log::error('Error fetching roles: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading roles.');
        }
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        try {
            return view('roles.create');
        } catch (\Exception $e) {
            Log::error('Error loading create role form: ' . $e->getMessage());
            return redirect()->route('roles.index')->with('error', 'Could not load the form.');
        }
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        // Validation stays outside the try-catch so Laravel can redirect back with form errors natively
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:roles,name'],
            'slug'        => ['required', 'string', 'max:255', 'unique:roles,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string'],
            'is_default'  => ['nullable', 'boolean'],
        ], [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        ]);

        try {
            Role::create([
                'name'        => $validated['name'],
                'slug'        => Str::slug($validated['slug']),
                'description' => $validated['description'] ?? null,
                'is_default'  => $request->boolean('is_default'),
            ]);

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create role. Please try again.');
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        try {
            return view('roles.show', compact('role'));
        } catch (\Exception $e) {
            Log::error('Error showing role ' . $role->id . ': ' . $e->getMessage());
            return redirect()->route('roles.index')->with('error', 'Could not display the role.');
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        try {
            return view('roles.edit', compact('role'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form for role ' . $role->id . ': ' . $e->getMessage());
            return redirect()->route('roles.index')->with('error', 'Could not load the edit form.');
        }
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Validation outside try-catch
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'slug'        => ['required', 'string', 'max:255', 'unique:roles,slug,' . $role->id, 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string'],
            'is_default'  => ['nullable', 'boolean'],
        ], [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        ]);

        try {
            $role->update([
                'name'        => $validated['name'],
                'slug'        => Str::slug($validated['slug']),
                'description' => $validated['description'] ?? null,
                'is_default'  => $request->boolean('is_default'),
            ]);

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating role ' . $role->id . ': ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update role. Please try again.');
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        try {
            if ($role->is_default) {
                return redirect()
                    ->route('roles.index')
                    ->with('error', 'Default roles cannot be deleted.');
            }

            // Prevent deleting a role that has users assigned to it
            if ($role->users()->exists()) {
                return redirect()
                    ->route('roles.index')
                    ->with('error', 'Cannot delete a role that is assigned to users.');
            }

            $role->delete();

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting role ' . $role->id . ': ' . $e->getMessage());
            return redirect()
                ->route('roles.index')
                ->with('error', 'Failed to delete role. It might be in use.');
        }
    }
}