<script setup lang="ts">
import { computed } from 'vue';
import { useSidebar } from '@/composables/useSidebar';
import { useTranslations } from '@/composables/useTranslations';
import { detailMenus, resolveMenuRoute } from '@/config/menu';
import { resolveIcon } from '@/lib/resolveIcon';
import SidebarMenuGroup from './SidebarMenuGroup.vue';

const { activeMenu, isCollapsed } = useSidebar();
const { t } = useTranslations();

const activeSection = computed(() => detailMenus[activeMenu.value]);
const showPanel = computed(
    () =>
        !isCollapsed.value &&
        activeSection.value?.groups &&
        activeSection.value.groups.length > 0,
);

const translatedGroups = computed(() => {
    if (!activeSection.value) {
        return [];
    }

    return activeSection.value.groups.map((group) => ({
        ...group,
        title: t(group.titleKey),
        items: group.items.map((item) => ({
            ...item,
            title: t(item.titleKey),
            href: resolveMenuRoute(item.route) ?? '#',
            createHref: resolveMenuRoute(item.createRoute),
            icon: resolveIcon(item.icon),
        })),
    }));
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 -translate-x-4"
        enter-to-class="opacity-100 translate-x-0"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-x-0"
        leave-to-class="opacity-0 -translate-x-4"
    >
        <div
            v-if="showPanel"
            class="border-sidebar-border flex w-56 min-w-56 flex-col overflow-hidden border-r bg-sidebar/80 backdrop-blur-sm"
        >
            <!-- Panel Header -->
            <div
                class="border-sidebar-border/50 flex h-14 items-center border-b px-4"
            >
                <h2 class="text-sidebar-foreground text-base font-semibold">
                    {{ activeSection ? t(activeSection.titleKey) : '' }}
                </h2>
            </div>

            <!-- Panel Content -->
            <div class="flex-1 space-y-4 overflow-y-auto p-3">
                <SidebarMenuGroup
                    v-for="group in translatedGroups"
                    :key="group.titleKey"
                    :title="group.title"
                    :items="group.items"
                    :collapsible="group.collapsible"
                />
            </div>

            <!-- Panel Footer -->
            <div class="border-sidebar-border/50 border-t px-4 py-3">
                <p class="text-sidebar-foreground/40 text-[10px]">
                    &copy; 2026 Spaceworks Inc.
                </p>
            </div>
        </div>
    </Transition>
</template>
