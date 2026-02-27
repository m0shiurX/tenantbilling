<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useSidebar } from '@/composables/useSidebar';
import { useTranslations } from '@/composables/useTranslations';
import {
    mainSections,
    bottomItems,
    matchesPattern,
    resolveMainSections,
    resolveBottomItems,
} from '@/config/menu';

const page = usePage();
const { activeMenu, setActiveMenu, expand } = useSidebar();
const { t } = useTranslations();

const mainSectionItems = computed(() => resolveMainSections(mainSections, t));

const footerSectionItems = computed(() => resolveBottomItems(bottomItems, t));

const isSectionActive = (section: {
    activePatterns?: readonly string[];
    href?: string;
}) => {
    if (section.activePatterns && section.activePatterns.length > 0) {
        return matchesPattern(page.url, section.activePatterns);
    }

    return section.href ? page.url.startsWith(section.href) : false;
};

const handleSectionClick = (section: {
    id: string;
    action?: 'modal';
    modalId?: string;
    href?: string;
    disabled?: boolean;
}) => {
    // Don't navigate if disabled (coming soon)
    if (section.disabled) {
        return;
    }

    if (section.action === 'modal' && section.modalId) {
        window.dispatchEvent(new CustomEvent('open-help-modal'));
        return;
    }

    setActiveMenu(section.id);
    expand();

    // Navigate to the href if available
    if (section.href) {
        router.visit(section.href);
    }
};
</script>

<template>
    <div class="flex w-20 min-w-20 flex-col items-center bg-sidebar py-2">
        <!-- Logo -->
        <Link href="/dashboard" class="mb-2 flex h-14 w-14 items-center justify-center">
            <div class="bg-sidebar-primary flex h-10 w-10 items-center justify-center rounded-lg">
                <AppLogoIcon class="size-6 text-white" />
            </div>
        </Link>

        <!-- Main Menu Icons -->
        <nav class="flex w-full flex-col items-center gap-4 px-2">
            <div v-for="section in mainSectionItems" :key="section.id" class="flex w-full flex-col items-center">
                <button type="button" @click="handleSectionClick(section)" :class="[
                    'flex mb-1 h-12 p-2 bg-gray-100/10 w-12 items-center justify-center rounded-2xl transition-colors',
                    section.disabled
                        ? 'cursor-not-allowed opacity-40'
                        : 'hover:bg-sidebar-accent/20 hover:text-white',
                    'focus-visible:ring-sidebar-ring focus-visible:ring-2 focus-visible:outline-none',
                    activeMenu === section.id || isSectionActive(section)
                        ? 'bg-sidebar-accent text-white'
                        : 'text-white/70',
                ]" :title="section.disabled
                    ? `${section.title} (Coming Soon)`
                    : section.title
                    " :disabled="section.disabled">
                    <component :is="section.iconComponent" class="size-6" />
                </button>
                <span :class="[
                    'mt-01 text-center uppercase text-[10px] leading-tight transition-colors',
                    section.disabled ? 'opacity-40' : '',
                    activeMenu === section.id || isSectionActive(section)
                        ? 'font-medium text-white'
                        : 'text-white/70',
                ]">
                    {{ section.title }}
                </span>
            </div>
        </nav>

        <!-- Footer Icons -->
        <nav class="mt-auto flex w-full flex-col items-center gap-4 px-2 pb-2">
            <div v-for="section in footerSectionItems" :key="section.id" class="flex w-full flex-col items-center">
                <!-- Button for modal actions or navigation with detail menu toggle -->
                <button type="button" @click="handleSectionClick(section)" :class="[
                    'flex h-12 p-2 bg-gray-100/10 w-12 items-center justify-center rounded-2xl transition-colors',
                    'hover:bg-sidebar-accent/20 hover:text-white',
                    'focus-visible:ring-sidebar-ring focus-visible:ring-2 focus-visible:outline-none',
                    activeMenu === section.id || isSectionActive(section)
                        ? 'bg-sidebar-accent text-white'
                        : 'text-white/70',
                ]" :title="section.title">
                    <component :is="section.iconComponent" class="size-6" />
                </button>
                <span :class="[
                    'mt-1 text-center uppercase text-[10px] leading-tight transition-colors',
                    activeMenu === section.id || isSectionActive(section)
                        ? 'font-medium text-white'
                        : 'text-white/70',
                ]">
                    {{ section.title }}
                </span>
            </div>
        </nav>
    </div>
</template>
