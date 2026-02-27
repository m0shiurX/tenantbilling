<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Default roles to create.
     *
     * @var array<string, array{description: string, is_super_admin: bool}>
     */
    protected array $roles = [
        'Super Admin' => [
            'description' => 'Full system access - bypasses all permission checks via Gate::before',
            'is_super_admin' => true,
        ],
        'Admin' => [
            'description' => 'Administrative access with all permissions except system management',
            'is_super_admin' => false,
        ],
        'User' => [
            'description' => 'Basic user access with limited permissions',
            'is_super_admin' => false,
        ],
    ];

    /**
     * Permissions to exclude from Admin role.
     *
     * @var array<int, string>
     */
    protected array $adminRestrictedPrefixes = [
        'user_management_',
        'permission_',
        'role_',
        'user_',
        'backup_',
    ];

    /**
     * Permissions allowed for User role.
     *
     * @var array<int, string>
     */
    protected array $userAllowedPermissions = [
        'profile_password_edit',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->syncPermissions();
        $this->createRoles();
        $this->createDefaultSuperAdmin();

        // Refresh cache after seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✓ Roles and permissions seeded successfully!');
    }

    /**
     * Sync all permissions from config - creates new ones and removes stale ones.
     */
    protected function syncPermissions(): void
    {
        $config = config('permissions', []);
        $configPermissions = [];

        // Collect all permission names from config
        foreach ($config as $feature => $definition) {
            $group = $definition['group'] ?? null;

            if (isset($definition['abilities'])) {
                foreach ($definition['abilities'] as $ability) {
                    $permissionName = "{$feature}_{$ability}";
                    $configPermissions[$permissionName] = $group;
                }
            }

            if (isset($definition['custom'])) {
                foreach ($definition['custom'] as $custom) {
                    $permissionName = "{$feature}_{$custom}";
                    $configPermissions[$permissionName] = $group;
                }
            }
        }

        // Create or update permissions from config
        $created = 0;
        $updated = 0;
        foreach ($configPermissions as $name => $group) {
            $permission = Permission::where('name', $name)->where('guard_name', 'web')->first();

            if (! $permission) {
                Permission::create(['name' => $name, 'guard_name' => 'web', 'group' => $group]);
                $created++;
            } elseif ($permission->group !== $group) {
                $permission->update(['group' => $group]);
                $updated++;
            }
        }

        // Delete permissions that are no longer in config
        $deleted = Permission::where('guard_name', 'web')
            ->whereNotIn('name', array_keys($configPermissions))
            ->delete();

        $this->command->info("  Permissions: {$created} created, {$updated} updated, {$deleted} removed");
    }

    /**
     * Create roles and assign permissions.
     */
    protected function createRoles(): void
    {
        $allPermissions = Permission::all();

        foreach ($this->roles as $roleName => $config) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );

            if ($config['is_super_admin']) {
                // Super Admin doesn't need permissions - uses Gate::before
                $this->command->info("  Role: {$roleName} (uses Gate::before for all access)");
            } elseif ($roleName === 'User') {
                // User gets only specific allowed permissions
                $permissions = $allPermissions->filter(
                    fn($p) => in_array($p->name, $this->userAllowedPermissions)
                );
                $role->syncPermissions($permissions);
                $this->command->info("  Role: {$roleName} with {$permissions->count()} permissions");
            } else {
                // Admin gets all permissions except restricted ones
                $permissions = $allPermissions->filter(function ($permission) {
                    foreach ($this->adminRestrictedPrefixes as $prefix) {
                        if (str_starts_with($permission->name, $prefix)) {
                            return false;
                        }
                    }

                    return true;
                });

                $role->syncPermissions($permissions);
                $this->command->info("  Role: {$roleName} with {$permissions->count()} permissions");
            }
        }
    }

    /**
     * Create the default Super Admin user.
     */
    protected function createDefaultSuperAdmin(): void
    {
        $email = config('app.super_admin_email', 'admin@app.test');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => bcrypt(config('app.super_admin_password', 'password')),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
        }

        $this->command->info("  Default Super Admin: {$email}");
    }
}
