<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BackArrowIcon } from '@/components/Icons';
import AppLayout from '@/layouts/AppLayout.vue';

interface PermissionItem {
    id: number;
    name: string;
    group?: string;
    created_at: string;
}

interface PermissionGroup {
    name: string;
    permissions: PermissionItem[];
}

interface Props {
    permissionGroups: PermissionGroup[];
}

const props = defineProps<Props>();

const selectedGroup = ref<string | null>(null);

const allPermissions = computed(() => {
    return props.permissionGroups.flatMap((g) =>
        g.permissions.map((p) => ({ ...p, group: g.name })),
    );
});

const filteredPermissions = computed(() => {
    if (selectedGroup.value === null) {
        return allPermissions.value;
    }
    return allPermissions.value.filter((p) => p.group === selectedGroup.value);
});

const totalCount = computed(() => allPermissions.value.length);
</script>

<template>
    <AppLayout>
        <Head title="Permissions" />

        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4">
                    <Link
                        href="/settings"
                        class="group rounded-xl border border-primary-light bg-linear-to-br from-primary-lighter to-primary-light/50 p-2.5 transition-all duration-300 hover:from-primary-light hover:to-primary-lighter hover:shadow-md hover:shadow-primary-light/50"
                    >
                        <BackArrowIcon
                            class="h-5 w-5 text-primary transition-transform duration-300 group-hover:-translate-x-0.5"
                        />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-text">
                            Permissions
                        </h1>
                        <p class="mt-1 text-sm text-text-muted">
                            View system access control rules
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info Banner -->
            <div
                class="mb-6 flex items-start gap-3 rounded-lg border border-info/30 bg-info-light px-4 py-3 text-info"
            >
                <svg
                    class="mt-0.5 h-5 w-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <div>
                    <p class="font-medium">Read Only</p>
                    <p class="mt-1 text-sm opacity-90">
                        Permissions are defined in code and cannot be modified
                        here. Use the Roles page to assign permissions.
                    </p>
                </div>
            </div>

            <!-- Filter by Group -->
            <div class="mb-4 flex flex-wrap gap-2">
                <button
                    @click="selectedGroup = null"
                    :class="
                        selectedGroup === null
                            ? 'bg-primary text-white'
                            : 'bg-surface-alt text-text-muted hover:bg-border'
                    "
                    class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors"
                >
                    All Permissions ({{ totalCount }})
                </button>
                <button
                    v-for="group in permissionGroups"
                    :key="group.name"
                    @click="selectedGroup = group.name"
                    :class="
                        selectedGroup === group.name
                            ? 'bg-primary text-white'
                            : 'bg-surface-alt text-text-muted hover:bg-border'
                    "
                    class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors"
                >
                    {{ group.name }} ({{ group.permissions.length }})
                </button>
            </div>

            <!-- Permissions List Card -->
            <div
                class="overflow-hidden rounded-lg border border-white bg-white/50 shadow-sm shadow-gray-50"
            >
                <div
                    class="border-b border-border bg-linear-to-r from-surface to-surface-alt px-6 py-4"
                >
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-surface-alt p-2">
                            <svg
                                class="h-5 w-5 text-text-muted"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-text">
                                Total Permissions
                            </h3>
                            <p class="text-sm text-text-muted">
                                {{ filteredPermissions.length }} permissions
                            </p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-border">
                    <div
                        v-if="filteredPermissions.length === 0"
                        class="p-6 text-center text-text-muted"
                    >
                        No permissions found
                    </div>

                    <div
                        v-for="item in filteredPermissions"
                        :key="item.id"
                        class="flex items-center justify-between gap-4 p-4 transition-colors hover:bg-surface"
                    >
                        <div class="flex flex-1 items-center gap-4">
                            <div class="flex-1">
                                <p
                                    class="font-mono text-sm font-medium text-text"
                                >
                                    {{ item.name }}
                                </p>
                            </div>
                            <span
                                class="rounded-full bg-primary-lighter px-2 py-1 text-xs font-medium text-primary"
                            >
                                {{ item.group }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
