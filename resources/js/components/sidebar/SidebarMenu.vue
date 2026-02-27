<template>
    <div
        class="w-55 overflow-y-auto border-r border-white/5 bg-primary-dark/95 text-white/95 backdrop-blur-sm"
    >
        <div :key="activeMenu">
            <!-- Menu Title -->
            <div class="flex h-16 w-full items-center px-6 pt-3">
                <div
                    class="font-heading text-lg font-light text-white/90 capitalize dark:text-white"
                >
                    {{ menuTitle }}
                </div>
            </div>

            <!-- Dynamic Menu based on active section -->
            <div class="menu sub-menu active-menu space-y-4 px-3 pb-4">
                <template
                    v-for="group in filteredActiveGroups"
                    :key="group.titleKey"
                >
                    <SidebarMenuGroup
                        :title="t(group.titleKey)"
                        :icon="group.icon"
                        :items="translateItems(filterItems(group.items))"
                        :collapsible="group.collapsible || false"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useFeatures } from '@/composables/useFeatures';
import { useTranslations } from '@/composables/useTranslations';
import {
    detailMenus,
    translateMenuItems,
    type MenuItem,
    type MenuGroup,
} from '@/config/menu';
import SidebarMenuGroup from './SidebarMenuGroup.vue';

const { t } = useTranslations();
const { isEnabled, isAgro } = useFeatures();

const props = defineProps<{
    activeMenu: string;
}>();

// Access the groups array from the active detail menu
const activeGroups = computed(
    () =>
        detailMenus[props.activeMenu]?.groups ||
        detailMenus.dashboard?.groups ||
        [],
);

// Filter items based on requiredFeature and requiredBusinessType
function filterItems(items: MenuItem[]): MenuItem[] {
    return items.filter((item) => {
        // Check feature flag requirement
        if (item.requiredFeature && !isEnabled(item.requiredFeature)) {
            return false;
        }
        // Check business type requirement (e.g., 'agro' for fertilizer reports)
        if (item.requiredBusinessType === 'agro' && !isAgro.value) {
            return false;
        }
        return true;
    });
}

// Filter out groups that have no visible items after feature filtering
// Also filter groups that have a requiredFeature that is not enabled
const filteredActiveGroups = computed(() => {
    return activeGroups.value
        .filter((group: MenuGroup) => {
            // If group has requiredFeature, check if it's enabled
            if (group.requiredFeature) {
                return isEnabled(group.requiredFeature);
            }
            return true;
        })
        .map((group: MenuGroup) => ({
            ...group,
            items: filterItems(group.items),
        }))
        .filter((group: MenuGroup) => group.items.length > 0);
});

const menuTitle = computed(() => {
    const titleKey = detailMenus[props.activeMenu]?.titleKey || 'nav.dashboard';
    return t(titleKey);
});

// Translate menu items using the utility function
function translateItems(items: MenuItem[]) {
    return translateMenuItems(filterItems(items), t);
}
</script>
