<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(): Response
    {
        $this->authorize('permission_access');

        $permissions = Permission::all()
            ->groupBy('group')
            ->map(fn ($group, $groupName) => [
                'name' => $groupName ?? 'Ungrouped',
                'permissions' => $group->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'created_at' => $p->created_at,
                ]),
            ])
            ->values();

        return Inertia::render('settings/permissions/Index', [
            'permissionGroups' => $permissions,
        ]);
    }
}
