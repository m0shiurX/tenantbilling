<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { PencilIcon } from 'lucide-vue-next';
import SettingsCard from '@/components/SettingsCard.vue';
import SettingsListItem from '@/components/SettingsListItem.vue';
import { getInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <AppLayout>
        <Head title="Settings" />

        <div class="mx-auto max-w-5xl px-3 py-6 sm:px-6">
            <!-- Header -->
            <div class="mb-4 sm:mb-8">
                <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                    Settings
                </h1>
                <p class="mt-0.5 text-xs text-gray-500 sm:mt-1 sm:text-sm">
                    Manage your business configuration
                </p>
            </div>

            <!-- User Info Bar - Compact on Mobile -->
            <div
                class="mb-4 rounded-xl border border-blue-100 bg-linear-to-r from-blue-50 to-indigo-50 p-3 shadow-sm sm:mb-8 sm:p-4"
            >
                <div class="flex items-center gap-3 sm:gap-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-blue-100 to-indigo-100 shadow-sm ring-2 ring-white sm:h-12 sm:w-12"
                    >
                        <svg
                            class="h-5 w-5 text-blue-500 sm:h-7 sm:w-7"
                            fill="currentColor"
                            viewBox="0 0 256 256"
                        >
                            <path
                                d="M192 96a64 64 0 1 1-64-64a64 64 0 0 1 64 64"
                                opacity=".2"
                            ></path>
                            <path
                                d="M230.92 212c-15.23-26.33-38.7-45.21-66.09-54.16a72 72 0 1 0-73.66 0c-27.39 8.94-50.86 27.82-66.09 54.16a8 8 0 1 0 13.85 8c18.84-32.56 52.14-52 89.07-52s70.23 19.44 89.07 52a8 8 0 1 0 13.85-8M72 96a56 56 0 1 1 56 56a56.06 56.06 0 0 1-56-56"
                            ></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p
                            class="truncate text-sm font-semibold text-gray-900 sm:text-base"
                        >
                            {{ user.name }}
                        </p>
                        <p class="truncate text-xs text-gray-500 sm:text-sm">
                            {{ user.email }}
                        </p>
                    </div>
                    <Link
                        href="/account"
                        class="flex items-center gap-1 rounded-lg bg-white/60 px-2 py-1 text-xs font-medium text-blue-600 transition-colors hover:bg-white hover:text-blue-700 sm:gap-1.5 sm:px-3 sm:py-1.5 sm:text-sm"
                    >
                        <PencilIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">My Account</span>
                        <span class="sm:hidden">Edit</span>
                    </Link>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- MOBILE VIEW: Compact List Layout          -->
            <!-- ========================================== -->
            <div class="space-y-3 sm:hidden">
                <!-- Business Section -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white"
                >
                    <div class="border-b border-gray-100 bg-gray-50 px-3 py-2">
                        <h2
                            class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase"
                        >
                            Business
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <SettingsListItem
                            title="Company Profile"
                            href="/settings/company"
                            icon="building"
                            color="blue"
                            :disabled="true"
                            :comingSoon="true"
                        />
                        <SettingsListItem
                            title="Invoice Settings"
                            href="/settings/invoice"
                            icon="document"
                            color="blue"
                            :disabled="true"
                            :comingSoon="true"
                        />
                    </div>
                </div>

                <!-- Access Control Section -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white"
                >
                    <div class="border-b border-gray-100 bg-gray-50 px-3 py-2">
                        <h2
                            class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase"
                        >
                            Access Control
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <SettingsListItem
                            title="Users"
                            href="/settings/users"
                            icon="users"
                            color="purple"
                        />
                        <SettingsListItem
                            title="Roles"
                            href="/settings/roles"
                            icon="shield"
                            color="purple"
                        />
                        <SettingsListItem
                            title="Permissions"
                            href="/settings/permissions"
                            icon="key"
                            color="purple"
                        />
                    </div>
                </div>

                <!-- System Section -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white"
                >
                    <div class="border-b border-gray-100 bg-gray-50 px-3 py-2">
                        <h2
                            class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase"
                        >
                            System
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <SettingsListItem
                            title="Backup & Restore"
                            href="/settings/backups"
                            icon="database"
                            color="orange"
                            :disabled="true"
                            :comingSoon="true"
                        />
                        <SettingsListItem
                            title="Activity Logs"
                            href="/settings/activity-logs"
                            icon="clipboard"
                            color="orange"
                            :disabled="true"
                            :comingSoon="true"
                        />
                        <SettingsListItem
                            title="Notifications"
                            href="/settings/notifications"
                            icon="bell"
                            color="orange"
                            :disabled="true"
                            :comingSoon="true"
                        />
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- DESKTOP VIEW: Card Grid Layout            -->
            <!-- ========================================== -->
            <div class="hidden space-y-6 sm:block">
                <!-- Business Section -->
                <section>
                    <div class="mb-3 flex items-center gap-2">
                        <div class="h-4 w-1 rounded-full bg-blue-500"></div>
                        <h2
                            class="text-xs font-semibold tracking-wider text-gray-500 uppercase"
                        >
                            Business
                        </h2>
                    </div>
                    <div
                        class="grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <SettingsCard
                            title="Company Profile"
                            description="Business name, logo, and contact info"
                            href="/settings/company"
                            icon="building"
                            color="blue"
                            :disabled="true"
                            :comingSoon="true"
                        />
                        <SettingsCard
                            title="Invoice Settings"
                            description="Configure invoice templates and numbering"
                            href="/settings/invoice"
                            icon="document"
                            color="blue"
                            :disabled="true"
                            :comingSoon="true"
                        />
                    </div>
                </section>

                <!-- Access Control Section -->
                <section>
                    <div class="mb-3 flex items-center gap-2">
                        <div class="h-4 w-1 rounded-full bg-purple-500"></div>
                        <h2
                            class="text-xs font-semibold tracking-wider text-gray-500 uppercase"
                        >
                            Access Control
                        </h2>
                    </div>
                    <div
                        class="grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <SettingsCard
                            title="Users"
                            description="Manage team members and their access"
                            href="/settings/users"
                            icon="users"
                            color="purple"
                        />
                        <SettingsCard
                            title="Roles"
                            description="Define user roles and permissions"
                            href="/settings/roles"
                            icon="shield"
                            color="purple"
                        />
                        <SettingsCard
                            title="Permissions"
                            description="View system access control rules"
                            href="/settings/permissions"
                            icon="key"
                            color="purple"
                        />
                    </div>
                </section>

                <!-- System Section -->
                <section>
                    <div class="mb-3 flex items-center gap-2">
                        <div class="h-4 w-1 rounded-full bg-orange-500"></div>
                        <h2
                            class="text-xs font-semibold tracking-wider text-gray-500 uppercase"
                        >
                            System
                        </h2>
                    </div>
                    <div
                        class="grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <SettingsCard
                            title="Backup & Restore"
                            description="Manage database backups"
                            href="/settings/backups"
                            icon="database"
                            color="orange"
                            :disabled="true"
                            :comingSoon="true"
                        />
                        <SettingsCard
                            title="Activity Logs"
                            description="View system audit trail"
                            href="/settings/activity-logs"
                            icon="clipboard"
                            color="orange"
                            :disabled="true"
                            :comingSoon="true"
                        />
                        <SettingsCard
                            title="Notifications"
                            description="Configure alert preferences"
                            href="/settings/notifications"
                            icon="bell"
                            color="orange"
                            :disabled="true"
                            :comingSoon="true"
                        />
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
