<template>
    <div class="relative" ref="dropdownRef">
        <!-- Bell Button -->
        <button
            @click="toggleDropdown"
            class="btn btn-circle relative h-9 min-h-9 w-9 bg-slate-300 dash-ring"
            :aria-label="t('notifications.in_app.bell_label')"
        >
            <BellIcon class-name="size-5" aria-hidden="true" />
            <!-- Unread Badge -->
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown Panel -->
        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-4 py-3"
                >
                    <h3 class="font-semibold text-gray-900">
                        {{ t('notifications.in_app.title') }}
                    </h3>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllRead"
                        class="cursor-pointer text-xs font-medium text-purple-600 hover:text-purple-700 hover:underline"
                    >
                        {{ t('notifications.in_app.mark_all_read') }}
                    </button>
                </div>

                <!-- Notifications List -->
                <div
                    v-if="notifications.length > 0"
                    class="max-h-96 overflow-y-auto"
                >
                    <div
                        v-for="notification in notifications"
                        :key="notification.id"
                        class="relative"
                    >
                        <component
                            :is="notification.url ? 'a' : 'div'"
                            :href="notification.url"
                            @click="handleNotificationClick(notification)"
                            class="flex cursor-pointer gap-3 border-b border-gray-50 px-4 py-3 transition-colors hover:bg-gray-50"
                            :class="{
                                'bg-purple-50/50': !notification.read_at,
                            }"
                        >
                            <!-- Icon -->
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full"
                                :class="getIconBgClass(notification.icon)"
                            >
                                <component
                                    :is="getIcon(notification.icon)"
                                    class-name="size-5"
                                    :class="
                                        getIconColorClass(notification.icon)
                                    "
                                />
                            </div>

                            <!-- Content -->
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-medium text-gray-900"
                                >
                                    {{ notification.title }}
                                </p>
                                <p class="line-clamp-2 text-xs text-gray-500">
                                    {{ notification.message }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ notification.time_ago }}
                                </p>
                            </div>

                            <!-- Unread indicator -->
                            <div
                                v-if="!notification.read_at"
                                class="mt-1 flex-shrink-0"
                            >
                                <span
                                    class="block h-2 w-2 rounded-full bg-purple-500"
                                ></span>
                            </div>
                        </component>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center px-4 py-8 text-center"
                >
                    <BellIcon class-name="size-12 text-gray-300 mb-3" />
                    <p class="text-sm text-gray-500">
                        {{ t('notifications.in_app.empty') }}
                    </p>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                    <Link
                        :href="route('admin.notifications.index')"
                        class="flex items-center justify-center gap-1 text-sm font-medium text-purple-600 hover:text-purple-700"
                        @click="isOpen = false"
                    >
                        {{ t('notifications.in_app.view_all') }}
                        <ChevronRightIcon class-name="size-4" />
                    </Link>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { Link, usePage, usePoll, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import {
    BellIcon,
    ChevronRightIcon,
    InvoiceIcon,
    PurchaseIcon,
    WalletIcon,
    WarningIcon,
    ClockIcon,
    CheckIcon,
} from '@/components/Icons';
import { useTranslations } from '@/composables/useTranslations.js';
import { safeRoute as route } from '@/utils/safeRoute.js';

const { t } = useTranslations();
const page = usePage();

const isOpen = ref(false);
const dropdownRef = ref(null);

// Get notifications from shared Inertia props (reactive)
const notifications = computed(() => page.props.notifications?.items || []);
const unreadCount = computed(() => page.props.notifications?.unread_count || 0);

// Use Inertia polling - poll every 30 seconds for notifications only
// This uses partial reloads so only 'notifications' prop is fetched
// Throttles to 10% speed when tab is in background (default behavior)
usePoll(30000, {
    only: ['notifications'],
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const handleNotificationClick = async (notification) => {
    if (!notification.read_at) {
        try {
            await fetch(
                route('admin.notifications.mark-read', { id: notification.id }),
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]',
                        )?.content,
                        'Content-Type': 'application/json',
                    },
                },
            );
            // Immediately reload notifications to update badge count
            router.reload({ only: ['notifications'] });
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    if (notification.url) {
        isOpen.value = false;
    }
};

const markAllRead = async () => {
    try {
        await fetch(route('admin.notifications.mark-all-read'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]',
                )?.content,
                'Content-Type': 'application/json',
            },
        });
        // Immediately reload notifications to update badge count
        router.reload({ only: ['notifications'] });
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

const getIcon = (iconType) => {
    const icons = {
        sale: InvoiceIcon,
        purchase: PurchaseIcon,
        payment: WalletIcon,
        warning: WarningIcon,
        reminder: ClockIcon,
        success: CheckIcon,
        bell: BellIcon,
    };
    return icons[iconType] || BellIcon;
};

const getIconBgClass = (iconType) => {
    const classes = {
        sale: 'bg-emerald-100',
        purchase: 'bg-blue-100',
        payment: 'bg-purple-100',
        warning: 'bg-amber-100',
        reminder: 'bg-orange-100',
        success: 'bg-green-100',
        bell: 'bg-gray-100',
    };
    return classes[iconType] || 'bg-gray-100';
};

const getIconColorClass = (iconType) => {
    const classes = {
        sale: 'text-emerald-600',
        purchase: 'text-blue-600',
        payment: 'text-purple-600',
        warning: 'text-amber-600',
        reminder: 'text-orange-600',
        success: 'text-green-600',
        bell: 'text-gray-600',
    };
    return classes[iconType] || 'text-gray-600';
};

// Close dropdown on outside click
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
