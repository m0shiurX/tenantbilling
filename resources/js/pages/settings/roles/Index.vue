<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { BackArrowIcon, SuccessCheckIcon, PlusIcon } from '@/components/Icons';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Role } from '@/types';

interface RoleWithCounts extends Role {
    users_count: number;
    permissions_count: number;
}

interface Props {
    roles: RoleWithCounts[];
}

const props = defineProps<Props>();

const deleteDialogOpen = ref(false);
const roleToDelete = ref<RoleWithCounts | null>(null);

function confirmDelete(role: RoleWithCounts) {
    roleToDelete.value = role;
    deleteDialogOpen.value = true;
}

function deleteRole() {
    if (roleToDelete.value) {
        router.delete(`/settings/roles/${roleToDelete.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                deleteDialogOpen.value = false;
                roleToDelete.value = null;
            },
        });
    }
}

function isSystemRole(role: RoleWithCounts): boolean {
    return role.name === 'Super Admin';
}
</script>

<template>
    <AppLayout>
        <Head title="Roles" />

        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link
                            href="/settings"
                            class="group rounded-xl border border-primary-light bg-linear-to-br from-primary-lighter to-primary-light/50 p-2.5 transition-all duration-300 hover:from-primary-light hover:to-primary-lighter hover:shadow-md hover:shadow-primary-light/50"
                        >
                            <BackArrowIcon
                                class="h-5 w-5 text-primary transition-transform duration-300 group-hover:-translate-x-0.5"
                            />
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold text-text">Roles</h1>
                            <p class="mt-1 text-sm text-text-muted">
                                Manage user roles and their permissions
                            </p>
                        </div>
                    </div>
                    <Link
                        href="/settings/roles/create"
                        class="flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-all duration-300 hover:bg-primary-dark hover:shadow-md hover:shadow-primary-light/50"
                    >
                        <PlusIcon class="h-4 w-4" />
                        Add Role
                    </Link>
                </div>
            </div>

            <!-- Flash Messages -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 flex items-center gap-3 rounded-xl border border-success/30 bg-success-light px-5 py-4 text-success shadow-sm shadow-success-light/50"
            >
                <div class="rounded-lg bg-success/10 p-1.5">
                    <SuccessCheckIcon class="h-5 w-5" />
                </div>
                <span class="font-medium">{{ $page.props.flash.success }}</span>
            </div>

            <!-- Roles List -->
            <div
                class="overflow-hidden rounded-lg border border-white bg-white/50 shadow-sm shadow-surface-alt"
            >
                <table class="w-full">
                    <thead class="bg-surface/50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-text-muted"
                            >
                                Role Name
                            </th>
                            <th
                                class="px-4 py-3 text-center text-sm font-medium text-text-muted"
                            >
                                Permissions
                            </th>
                            <th
                                class="px-4 py-3 text-center text-sm font-medium text-text-muted"
                            >
                                Users
                            </th>
                            <th
                                class="px-4 py-3 text-center text-sm font-medium text-text-muted"
                            >
                                Type
                            </th>
                            <th
                                class="px-4 py-3 text-right text-sm font-medium text-text-muted"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="role in roles"
                            :key="role.id"
                            class="hover:bg-surface/50"
                        >
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-text">{{
                                        role.name
                                    }}</span>
                                    <span
                                        v-if="isSystemRole(role)"
                                        class="rounded bg-warning-light px-2 py-0.5 text-xs text-warning-dark"
                                    >
                                        Protected
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="rounded bg-info-light px-2 py-1 text-sm text-info"
                                >
                                    {{
                                        isSystemRole(role)
                                            ? 'All'
                                            : role.permissions_count
                                    }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="rounded bg-surface-alt px-2 py-1 text-sm text-text-muted"
                                >
                                    {{ role.users_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    v-if="isSystemRole(role)"
                                    class="rounded bg-success-light px-2 py-0.5 text-xs text-success"
                                >
                                    System
                                </span>
                                <span
                                    v-else
                                    class="rounded bg-surface-alt px-2 py-0.5 text-xs text-text-muted"
                                >
                                    Custom
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        v-if="!isSystemRole(role)"
                                        :href="`/settings/roles/${role.id}/edit`"
                                        class="text-sm font-medium text-primary hover:text-primary-dark"
                                    >
                                        Edit
                                    </Link>
                                    <span
                                        v-else
                                        class="text-sm text-text-light"
                                    >
                                        Edit
                                    </span>
                                    <button
                                        v-if="
                                            !isSystemRole(role) &&
                                            role.users_count === 0
                                        "
                                        @click="confirmDelete(role)"
                                        class="text-sm font-medium text-danger hover:text-danger/80"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="roles.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-text-muted"
                            >
                                No roles found. Create your first role to get
                                started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="deleteDialogOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @click.self="deleteDialogOpen = false"
        >
            <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-semibold text-text">
                    Delete Role
                </h3>
                <p class="mb-6 text-text-muted">
                    Are you sure you want to delete the role
                    <strong>{{ roleToDelete?.name }}</strong
                    >? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="deleteDialogOpen = false"
                        class="px-4 py-2 font-medium text-text-muted hover:text-text"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteRole"
                        class="rounded-lg bg-danger px-4 py-2 font-medium text-white hover:bg-danger/90"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
