<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BackArrowIcon } from '@/components/Icons';
import AppLayout from '@/layouts/AppLayout.vue';

interface Props {
    permissionGroups: Record<string, Record<number, string>>;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    permissions: [] as string[],
});

function togglePermission(permissionName: string) {
    const index = form.permissions.indexOf(permissionName);
    if (index === -1) {
        form.permissions.push(permissionName);
    } else {
        form.permissions.splice(index, 1);
    }
}

function toggleGroupPermissions(groupPermissions: Record<number, string>) {
    const permissionNames = Object.values(groupPermissions);
    const allSelected = permissionNames.every((p) =>
        form.permissions.includes(p),
    );

    if (allSelected) {
        permissionNames.forEach((p) => {
            const index = form.permissions.indexOf(p);
            if (index !== -1) {
                form.permissions.splice(index, 1);
            }
        });
    } else {
        permissionNames.forEach((p) => {
            if (!form.permissions.includes(p)) {
                form.permissions.push(p);
            }
        });
    }
}

function isGroupFullySelected(
    groupPermissions: Record<number, string>,
): boolean {
    return Object.values(groupPermissions).every((p) =>
        form.permissions.includes(p),
    );
}

function formatPermissionName(name: string): string {
    return name
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

function submit() {
    form.post('/settings/roles');
}
</script>

<template>
    <AppLayout>
        <Head title="Create Role" />

        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4">
                    <Link
                        href="/settings/roles"
                        class="group rounded-xl border border-primary-light bg-linear-to-br from-primary-lighter to-primary-light/50 p-2.5 transition-all duration-300 hover:from-primary-light hover:to-primary-lighter hover:shadow-md hover:shadow-primary-light/50"
                    >
                        <BackArrowIcon
                            class="h-5 w-5 text-primary transition-transform duration-300 group-hover:-translate-x-0.5"
                        />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-text">
                            Create Role
                        </h1>
                        <p class="mt-1 text-sm text-text-muted">
                            Define a new role with specific permissions
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div
                class="rounded-lg border border-white bg-white/50 p-6 shadow-sm shadow-surface-alt"
            >
                <form @submit.prevent="submit">
                    <!-- Role Name -->
                    <div class="mb-6">
                        <label
                            for="name"
                            class="mb-1 block text-sm font-medium text-text-muted"
                        >
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full max-w-md rounded-lg border border-border px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none"
                            placeholder="e.g., Editor, Viewer, Manager"
                            required
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-sm text-danger"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-6">
                        <label
                            class="mb-3 block text-sm font-medium text-text-muted"
                        >
                            Permissions
                        </label>

                        <div class="space-y-4">
                            <div
                                v-for="(permissions, group) in permissionGroups"
                                :key="group"
                                class="rounded-lg border border-border bg-white p-4"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <h4 class="font-medium text-text">
                                        {{ group || 'Ungrouped' }}
                                    </h4>
                                    <button
                                        type="button"
                                        @click="
                                            toggleGroupPermissions(permissions)
                                        "
                                        class="text-xs font-medium text-primary hover:text-primary-dark"
                                    >
                                        {{
                                            isGroupFullySelected(permissions)
                                                ? 'Deselect All'
                                                : 'Select All'
                                        }}
                                    </button>
                                </div>
                                <div
                                    class="grid grid-cols-1 gap-2 lg:grid-cols-3 sm:grid-cols-2"
                                >
                                    <label
                                        v-for="(
                                            permissionName, permissionId
                                        ) in permissions"
                                        :key="permissionId"
                                        class="flex cursor-pointer items-center gap-2 rounded p-2 hover:bg-surface"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="
                                                form.permissions.includes(
                                                    permissionName,
                                                )
                                            "
                                            @change="
                                                togglePermission(permissionName)
                                            "
                                            class="rounded border-border text-primary focus:ring-primary"
                                        />
                                        <span class="text-sm text-text-muted">{{
                                            formatPermissionName(permissionName)
                                        }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <p
                            v-if="form.errors.permissions"
                            class="mt-1 text-sm text-danger"
                        >
                            {{ form.errors.permissions }}
                        </p>

                        <p class="mt-3 text-sm text-text-muted">
                            {{ form.permissions.length }} permission(s) selected
                        </p>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center gap-4 border-t border-border pt-4"
                    >
                        <button
                            type="submit"
                            class="rounded-lg bg-primary px-6 py-2 text-white transition hover:bg-primary-dark disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            {{
                                form.processing ? 'Creating...' : 'Create Role'
                            }}
                        </button>
                        <Link
                            href="/settings/roles"
                            class="text-text-muted hover:text-text"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
