<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync
                            {--prune : Remove permissions not defined in config}
                            {--dry-run : Show what would be done without making changes}
                            {--force : Skip confirmation prompts}
                            {--sync-roles : Also sync role-permission assignments}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize permissions from config/permissions.php to database';

    /**
     * Permissions to exclude from non-super-admin roles.
     *
     * @var array<int, string>
     */
    protected array $adminRestrictedPrefixes = [
        'user_management_',
        'permission_',
        'role_',
        'user_',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Syncing permissions from config/permissions.php...');
        $this->newLine();

        // Reset cached permissions before starting
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $configPermissions = $this->getConfigPermissions();
        $dbPermissions = Permission::pluck('name')->toArray();

        $toCreate = $configPermissions->filter(fn (array $p): bool => ! in_array($p['name'], $dbPermissions));
        $toUpdate = $configPermissions->filter(fn (array $p): bool => in_array($p['name'], $dbPermissions));
        $orphaned = collect($dbPermissions)->filter(fn (string $name): bool => ! $configPermissions->pluck('name')->contains($name));

        // Show summary
        $this->displaySummary($toCreate, $toUpdate, $orphaned);

        if ($this->option('dry-run')) {
            $this->warn('Dry run mode - no changes made.');

            return self::SUCCESS;
        }

        // Create new permissions
        if ($toCreate->isNotEmpty()) {
            $this->createPermissions($toCreate);
        }

        // Update existing permissions (group changes)
        if ($toUpdate->isNotEmpty()) {
            $this->updatePermissions($toUpdate);
        }

        // Handle orphaned permissions
        if ($orphaned->isNotEmpty() && $this->option('prune')) {
            $this->pruneOrphanedPermissions($orphaned);
        }

        // Sync role-permission assignments
        if ($this->option('sync-roles')) {
            $this->syncRolePermissions();
        }

        // Reset cache after changes
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->newLine();
        $this->info('✓ Permission sync complete!');

        return self::SUCCESS;
    }

    /**
     * Get all permissions from config file.
     *
     * @return Collection<int, array{name: string, group: string|null}>
     */
    protected function getConfigPermissions(): Collection
    {
        $config = config('permissions', []);
        $permissions = collect();

        foreach ($config as $feature => $definition) {
            $group = $definition['group'] ?? null;

            // Handle standard abilities (access, create, edit, show, delete)
            if (isset($definition['abilities'])) {
                foreach ($definition['abilities'] as $ability) {
                    $permissions->push([
                        'name' => "{$feature}_{$ability}",
                        'group' => $group,
                    ]);
                }
            }

            // Handle custom permissions
            if (isset($definition['custom'])) {
                foreach ($definition['custom'] as $custom) {
                    $permissions->push([
                        'name' => "{$feature}_{$custom}",
                        'group' => $group,
                    ]);
                }
            }
        }

        return $permissions;
    }

    /**
     * Display summary of changes to be made.
     *
     * @param  Collection<int, array{name: string, group: string|null}>  $toCreate
     * @param  Collection<int, array{name: string, group: string|null}>  $toUpdate
     * @param  Collection<int, string>  $orphaned
     */
    protected function displaySummary(Collection $toCreate, Collection $toUpdate, Collection $orphaned): void
    {
        if ($toCreate->isNotEmpty()) {
            $this->info("Permissions to CREATE ({$toCreate->count()}):");
            $this->table(
                ['Name', 'Group'],
                $toCreate->map(fn (array $p): array => [$p['name'], $p['group'] ?? '-'])->toArray()
            );
            $this->newLine();
        }

        if ($orphaned->isNotEmpty()) {
            $this->warn("ORPHANED permissions in database ({$orphaned->count()}):");
            $this->warn('These permissions exist in DB but not in config:');
            $orphaned->each(fn (string $name) => $this->line("  - {$name}"));
            $this->newLine();

            if (! $this->option('prune')) {
                $this->comment('Use --prune flag to remove orphaned permissions.');
            }
        }

        if ($toCreate->isEmpty() && $orphaned->isEmpty()) {
            $this->info('All permissions are in sync!');
        }
    }

    /**
     * Create new permissions in the database.
     *
     * @param  Collection<int, array{name: string, group: string|null}>  $toCreate
     */
    protected function createPermissions(Collection $toCreate): void
    {
        if (! $this->option('force') && ! $this->confirm("Create {$toCreate->count()} new permissions?")) {
            return;
        }

        $created = 0;
        foreach ($toCreate as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'group' => $permission['group'],
                'guard_name' => 'web',
            ]);
            $created++;
            $this->line("  ✓ Created: {$permission['name']}");
        }

        $this->info("Created {$created} permissions.");
    }

    /**
     * Update existing permissions (e.g., group changes).
     *
     * @param  Collection<int, array{name: string, group: string|null}>  $toUpdate
     */
    protected function updatePermissions(Collection $toUpdate): void
    {
        $updated = 0;
        foreach ($toUpdate as $permission) {
            $existing = Permission::findByName($permission['name'], 'web');

            if ($existing && $existing->group !== $permission['group']) {
                $oldGroup = $existing->group ?? 'null';
                $existing->update(['group' => $permission['group']]);
                $updated++;
                $this->line("  ↻ Updated group: {$permission['name']} ({$oldGroup} → {$permission['group']})");
            }
        }

        if ($updated > 0) {
            $this->info("Updated {$updated} permission groups.");
        }
    }

    /**
     * Remove orphaned permissions from the database.
     *
     * @param  Collection<int, string>  $orphaned
     */
    protected function pruneOrphanedPermissions(Collection $orphaned): void
    {
        $this->warn('WARNING: Removing permissions will also remove role assignments!');

        if (! $this->option('force') && ! $this->confirm("Delete {$orphaned->count()} orphaned permissions?")) {
            return;
        }

        $deleted = 0;
        foreach ($orphaned as $name) {
            $permission = Permission::findByName($name, 'web');
            if ($permission) {
                $permission->delete();
                $deleted++;
                $this->line("  ✗ Deleted: {$name}");
            }
        }

        $this->info("Deleted {$deleted} orphaned permissions.");
    }

    /**
     * Sync role-permission assignments.
     *
     * - Super Admin bypasses permission checks via Gate::before
     * - Admin gets all permissions except restricted ones
     * - Other roles get all permissions except restricted ones
     */
    protected function syncRolePermissions(): void
    {
        $this->newLine();
        $this->info('Syncing role-permission assignments...');

        $allPermissions = Permission::all();
        $roles = Role::all();

        if ($roles->isEmpty()) {
            $this->warn('No roles found in database. Run `php artisan db:seed --class=RoleAndPermissionSeeder` first.');

            return;
        }

        // Filter out restricted permissions for non-super-admin roles
        $standardPermissions = $allPermissions->filter(function (Permission $permission): bool {
            foreach ($this->adminRestrictedPrefixes as $prefix) {
                if (str_starts_with($permission->name, $prefix)) {
                    return false;
                }
            }

            return true;
        });

        foreach ($roles as $role) {
            if ($role->name === 'Super Admin') {
                // Super Admin uses Gate::before - no permissions needed
                $this->line("  ✓ {$role->name}: bypasses all checks via Gate::before");
            } elseif ($role->name === 'Admin') {
                // Admin gets standard permissions (excluding restricted)
                $role->syncPermissions($standardPermissions);
                $this->line("  ✓ {$role->name}: synced {$standardPermissions->count()} permissions");
            } else {
                // Other roles get standard permissions
                $role->syncPermissions($standardPermissions);
                $this->line("  ✓ {$role->name}: synced {$standardPermissions->count()} permissions");
            }
        }

        $this->info('Role-permission assignments synced.');
    }
}
