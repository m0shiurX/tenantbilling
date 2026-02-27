<template>
    <!-- bg-linear-to-tr from-red-800 via-emerald-500 to-purple-600 -->
    <aside
        id="sidebar"
        class="fixed top-0 bottom-0 left-0 z-1040 max-w-full bg-primary-darker transition-all duration-300 ease-in-out print:hidden"
        :class="sidebarClasses"
    >
        <div class="flex h-full overflow-hidden">
            <!-- Icon Rail (Left Column) -->
            <SidebarIconRail />

            <!-- Detail Menu (Right Column) -->
            <SidebarMenu
                :active-menu="activeMenu"
                :class="{
                    hidden: isCollapsed && !isMobileOpen,
                    block: !isCollapsed || isMobileOpen,
                }"
            />
        </div>
    </aside>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, watch } from 'vue';
import { useSidebar } from '@/composables/useSidebar';
import {
    findActiveMainSection,
    mainSections,
    bottomItems,
} from '@/config/menu';
import SidebarIconRail from './SidebarIconRail.vue';
import SidebarMenu from './SidebarMenu.vue';

const {
    isCollapsed,
    activeMenu,
    setActiveMenu,
    isMobileOpen,
    closeMobile,
    shouldAutoClose,
} = useSidebar();
const page = usePage();

// Compute sidebar CSS classes using Tailwind
const sidebarClasses = computed(() => {
    const classes = [];

    // Mobile: hidden by default, show when isMobileOpen is true
    // Desktop (lg+): show by default, always visible (rail or full)
    if (isMobileOpen.value) {
        // Mobile open: translate to visible position & full width
        classes.push('translate-x-0', 'w-75');
    } else {
        // Desktop default: Hidden on mobile/small screens
        classes.push('-translate-x-full', 'lg:translate-x-0');

        if (isCollapsed.value) {
            // Collapsed Desktop: Show Rail only (w-20)
            classes.push('w-75', 'lg:w-20');
        } else {
            // Expanded Desktop: Show Full (w-75)
            classes.push('w-75');
        }
    }

    return classes;
});

// Watch for page URL changes (Inertia navigation) to close mobile sidebar
// Only auto-close if shouldAutoClose() returns true (not for main section clicks)
watch(
    () => page.url,
    () => {
        // Close mobile sidebar on navigation (unless keepMobileOpen flag is set)
        if (isMobileOpen.value && shouldAutoClose()) {
            closeMobile();
        }
    },
);

// On mount, detect active section from current route
onMounted(() => {
    const section = findActiveMainSection(page.url, mainSections, bottomItems);
    if (section) {
        setActiveMenu(section);
    }
});

// Watch for route changes to update active section
watch(
    () => page.url,
    (newRoute) => {
        if (newRoute) {
            const section = findActiveMainSection(
                newRoute,
                mainSections,
                bottomItems,
            );
            if (section) {
                setActiveMenu(section);
            }
        }
    },
);
</script>
