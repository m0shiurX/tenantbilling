<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { BackArrowIcon, SuccessCheckIcon, PlusIcon } from '@/components/Icons';
import AppLayout from '@/layouts/AppLayout.vue';
import { type User } from '@/types';

interface PaginatedUsers {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    users: PaginatedUsers;
    filters: {
        search?: string;
        status?: string;
        role?: string;
    };
    statuses: Array<{ value: string; label: string }>;
    roles: string[];
}

const props = defineProps<Props>();
const page = usePage();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const role = ref(props.filters.role ?? '');
const deleteDialogOpen = ref(false);
const userToDelete = ref<User | null>(null);

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

watch([status, role], () => {
    applyFilters();
});

function applyFilters() {
    router.get(
        '/settings/users',
        {
            search: search.value || undefined,
            status: status.value || undefined,
            role: role.value || undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

function clearFilters() {
    search.value = '';
    status.value = '';
    role.value = '';
    router.get('/settings/users', {}, { preserveState: true, replace: true });
}

function confirmDelete(user: User) {
    userToDelete.value = user;
    deleteDialogOpen.value = true;
}

function deleteUser() {
    if (userToDelete.value) {
        router.delete(`/settings/users/${userToDelete.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                deleteDialogOpen.value = false;
                userToDelete.value = null;
            },
        });
    }
}

function getInitials(name: string): string {
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.slice(0, 2).toUpperCase();
}
</script>

<template>
    <AppLayout>
        <Head title="Users" />

        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
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
                            <h1 class="text-2xl font-bold text-text">Users</h1>
                            <p class="mt-1 text-sm text-text-muted">
                                Manage team members and their access
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-text-muted"
                            >{{ users.total }} users</span
                        >
                        <Link
                            href="/settings/users/create"
                            class="flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-all duration-300 hover:bg-primary-dark hover:shadow-md hover:shadow-primary-light/50"
                        >
                            <PlusIcon class="h-4 w-4" />
                            Add User
                        </Link>
                    </div>
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

            <!-- Filters -->
            <div
                class="mb-6 rounded-lg border border-white bg-white/50 p-4 shadow-sm shadow-surface-alt"
            >
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="relative flex-1">
                        <svg
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-text-light"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search by name or email..."
                            class="w-full rounded-lg border border-border py-2 pr-4 pl-10 focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none"
                        />
                    </div>
                    <select
                        v-model="status"
                        class="rounded-lg border border-border px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none sm:w-40"
                    >
                        <option value="">All Status</option>
                        <option
                            v-for="s in statuses"
                            :key="s.value"
                            :value="s.value"
                        >
                            {{ s.label }}
                        </option>
                    </select>
                    <select
                        v-model="role"
                        class="rounded-lg border border-border px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none sm:w-40"
                    >
                        <option value="">All Roles</option>
                        <option v-for="r in roles" :key="r" :value="r">
                            {{ r }}
                        </option>
                    </select>
                    <button
                        v-if="search || status || role"
                        @click="clearFilters"
                        class="px-3 text-sm font-medium text-text-muted hover:text-text"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div
                class="overflow-x-auto rounded-lg border border-white bg-white/50 shadow-sm shadow-surface-alt"
            >
                <table class="w-full min-w-[700px]">
                    <thead class="bg-surface/50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-text-muted"
                            >
                                User
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-text-muted"
                            >
                                Roles
                            </th>
                            <th
                                class="px-4 py-3 text-center text-sm font-medium text-text-muted"
                            >
                                Status
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
                            v-for="user in users.data"
                            :key="user.id"
                            class="hover:bg-surface/50"
                        >
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-full bg-linear-to-br from-primary to-primary-dark text-sm font-medium text-white"
                                    >
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-text">
                                            {{ user.name }}
                                        </p>
                                        <p class="text-xs text-text-muted">
                                            {{ user.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="r in user.roles"
                                        :key="r.id"
                                        class="rounded bg-info-light px-2 py-0.5 text-xs text-info"
                                    >
                                        {{ r.name }}
                                    </span>
                                    <span
                                        v-if="!user.roles?.length"
                                        class="text-xs text-text-light"
                                    >
                                        No role
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    :class="[
                                        'rounded px-2 py-1 text-xs',
                                        user.status === 'active'
                                            ? 'bg-success-light text-success'
                                            : 'bg-danger-light text-danger',
                                    ]"
                                >
                                    {{
                                        user.status === 'active'
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="`/settings/users/${user.id}/edit`"
                                        class="text-sm font-medium text-primary hover:text-primary-dark"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="
                                            user.id !== page.props.auth.user.id
                                        "
                                        @click="confirmDelete(user)"
                                        class="text-sm font-medium text-danger hover:text-danger/80"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="4"
                                class="px-4 py-8 text-center text-text-muted"
                            >
                                No users found matching your criteria.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="mt-6 flex justify-center">
                <nav class="flex gap-1">
                    <template v-for="link in users.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            :class="[
                                'rounded-lg px-3 py-1.5 text-sm transition-colors',
                                link.active
                                    ? 'bg-primary text-white'
                                    : 'border border-border bg-white text-text-muted hover:bg-surface',
                            ]"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="cursor-not-allowed rounded-lg bg-surface-alt px-3 py-1.5 text-sm text-text-light"
                            v-html="link.label"
                        />
                    </template>
                </nav>
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
                    Delete User
                </h3>
                <p class="mb-6 text-text-muted">
                    Are you sure you want to delete
                    <strong>{{ userToDelete?.name }}</strong
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
                        @click="deleteUser"
                        class="rounded-lg bg-danger px-4 py-2 font-medium text-white hover:bg-danger/90"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
