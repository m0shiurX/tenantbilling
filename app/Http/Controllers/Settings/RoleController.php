<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreRoleRequest;
use App\Http\Requests\Settings\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(): Response
    {
        $this->authorize('role_access');

        $roles = Role::withCount(['users', 'permissions'])->get();

        return Inertia::render('settings/roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        $this->authorize('role_create');

        $permissions = Permission::all()
            ->groupBy('group')
            ->map(fn ($group) => $group->pluck('name', 'id'));

        return Inertia::render('settings/roles/Create', [
            'permissionGroups' => $permissions,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize('role_create');

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()
            ->route('settings.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): Response
    {
        $this->authorize('role_edit');

        $permissions = Permission::all()
            ->groupBy('group')
            ->map(fn ($group) => $group->pluck('name', 'id'));

        return Inertia::render('settings/roles/Edit', [
            'role' => $role->load('permissions'),
            'permissionGroups' => $permissions,
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('role_edit');

        // Prevent editing Super Admin role name
        if ($role->name === 'Super Admin' && $request->name !== 'Super Admin') {
            return back()->with('error', 'Cannot rename the Super Admin role.');
        }

        $role->update([
            'name' => $request->name,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()
            ->route('settings.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('role_delete');

        // Prevent deletion of Super Admin role
        if ($role->name === 'Super Admin') {
            return back()->with('error', 'Cannot delete the Super Admin role.');
        }

        // Check if role is in use
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect()
            ->route('settings.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
