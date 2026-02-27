<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { Component } from 'vue';
import SidebarMenuItem from './SidebarMenuItem.vue';

type SidebarMenuItem = {
    id?: string;
    title: string;
    href: string;
    icon?: Component;
    createHref?: string;
    activePatterns?: string[];
};

const props = defineProps<{
    title?: string;
    icon?: Component;
    items: SidebarMenuItem[];
    collapsible?: boolean;
}>();

const page = usePage();

const isItemActive = (item: SidebarMenuItem): boolean => {
    const url = page.url;
    if (!item.activePatterns || item.activePatterns.length === 0) {
        return url === item.href;
    }

    return item.activePatterns.some((pattern) => url.startsWith(pattern));
};

// Auto-open if any item is active
const hasActiveItem = computed(() =>
    props.items.some((item) => isItemActive(item)),
);
const isOpen = ref(hasActiveItem.value || !props.collapsible);
</script>

<template>
    <div class="pb-2">
        <!-- Collapsible Group -->
        <template v-if="collapsible">
            <details :open="isOpen" class="group">
                <summary
                    @click.prevent="isOpen = !isOpen"
                    class="flex cursor-pointer items-center justify-between rounded-lg bg-white/5 px-3 py-2 text-[13px] font-semibold tracking-wide text-white/90 uppercase transition select-none group-open:bg-white/10"
                >
                    <span>{{ title }}</span>
                    <ChevronDown
                        :class="[
                            'size-4 transition-transform duration-200',
                            isOpen ? 'rotate-180' : '',
                        ]"
                    />
                </summary>
                <div
                    v-show="isOpen"
                    class="mt-2 space-y-1 border-l border-white/10 pl-3"
                >
                    <SidebarMenuItem
                        v-for="item in items"
                        :key="item.id || item.title"
                        :item="item"
                    />
                </div>
            </details>
        </template>

        <!-- Non-collapsible Group -->
        <template v-else>
            <h2
                v-if="title"
                class="mb-2 px-1.5 text-base font-normal text-zinc-400/90"
            >
                {{ title }}
            </h2>
            <div class="space-y-2">
                <SidebarMenuItem
                    v-for="item in items"
                    :key="item.id || item.title"
                    :item="item"
                />
            </div>
        </template>
    </div>
</template>
