<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BackArrowIcon } from '@/components/Icons';
import AppLayout from '@/layouts/AppLayout.vue';

interface AccountItem {
    title: string;
    description: string;
    href: string;
    icon: string;
    iconBg: string;
    iconColor: string;
}

const page = usePage();
const user = page.props.auth.user;

// Compute initials for avatar
const initials = computed(() => {
    const name = user.name || '';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.slice(0, 2).toUpperCase();
});

const accountItems: AccountItem[] = [
    {
        title: 'Profile & Security',
        description: 'Update your profile information and password',
        href: '/account/profile',
        icon: 'user',
        iconBg: 'from-emerald-400 to-emerald-600',
        iconColor: 'text-white',
    },
    {
        title: 'Two-Factor Authentication',
        description: 'Add extra security to your account',
        href: '/account/two-factor',
        icon: 'shield',
        iconBg: 'from-blue-400 to-blue-600',
        iconColor: 'text-white',
    },
];

const iconPaths: Record<string, string> = {
    user: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    shield: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
};
</script>

<template>
    <AppLayout>
        <Head title="My Account" />

        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3">
                    <Link
                        href="/settings"
                        class="rounded-lg bg-gray-100 p-2 transition-colors hover:bg-gray-200"
                    >
                        <BackArrowIcon class="h-5 w-5 text-gray-600" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            My Account
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Manage your profile and security settings
                        </p>
                    </div>
                </div>
            </div>

            <!-- User Info Card -->
            <div
                class="mb-8 rounded-xl bg-linear-to-r from-emerald-500 to-emerald-600 p-4 text-white shadow-lg sm:p-6"
            >
                <div class="flex flex-col items-center gap-4 sm:flex-row">
                    <!-- Avatar -->
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border-2 border-white/30 bg-white/20 sm:h-20 sm:w-20"
                    >
                        <span
                            class="text-xl font-bold text-white sm:text-2xl"
                            >{{ initials }}</span
                        >
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <h2 class="text-xl font-bold">{{ user.name }}</h2>
                        <p class="text-sm text-emerald-100">{{ user.email }}</p>
                        <div
                            class="mt-2 flex flex-wrap justify-center gap-2 sm:justify-start"
                        >
                            <span
                                class="rounded-full bg-white/20 px-2 py-1 text-xs font-medium"
                                >Member since {{ user.created_at }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Options -->
            <div class="space-y-4">
                <Link
                    v-for="item in accountItems"
                    :key="item.title"
                    :href="item.href"
                    class="group flex items-center gap-4 rounded-lg border border-white bg-white/50 p-4 shadow-sm shadow-gray-50 transition-all hover:border-gray-100 hover:shadow-md"
                >
                    <!-- Icon -->
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br"
                        :class="item.iconBg"
                    >
                        <svg
                            class="h-6 w-6"
                            :class="item.iconColor"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                :d="iconPaths[item.icon]"
                            />
                        </svg>
                    </div>

                    <!-- Text -->
                    <div class="min-w-0 flex-1">
                        <h3
                            class="truncate font-semibold text-gray-900 transition-colors group-hover:text-emerald-600"
                        >
                            {{ item.title }}
                        </h3>
                        <p class="truncate text-sm text-gray-500">
                            {{ item.description }}
                        </p>
                    </div>

                    <!-- Arrow -->
                    <svg
                        class="h-5 w-5 shrink-0 text-gray-400 transition-colors group-hover:text-emerald-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
