<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Plus, Lock } from 'lucide-vue-next';
import { computed } from 'vue';
import type { Component } from 'vue';

const props = defineProps<{
    item: {
        id?: string;
        title: string;
        href: string;
        icon?: Component;
        createHref?: string;
        activePatterns?: string[];
        disabled?: boolean;
    };
}>();

const page = usePage();

const active = computed(() => {
    if (props.item.disabled) return false;

    const url = page.url;
    if (!props.item.activePatterns || props.item.activePatterns.length === 0) {
        return url === props.item.href;
    }

    return props.item.activePatterns.some((pattern) => url.startsWith(pattern));
});
</script>

<template>
    <!-- Disabled/Coming Soon Item -->
    <div
        v-if="item.disabled"
        class="flex w-full cursor-not-allowed items-center justify-between gap-2 rounded-r-lg border-l-[3px] border-transparent px-3 py-2 text-[13px] font-medium text-white/40"
    >
        <div class="flex items-center gap-2">
            <component
                :is="item.icon"
                v-if="item.icon"
                class="size-4 shrink-0 text-white/30"
            />
            <span class="truncate">{{ item.title }}</span>
        </div>
        <span
            class="inline-flex items-center gap-1 rounded-full bg-white/10 px-1.5 py-0.5 text-[10px] font-medium text-white/50"
        >
            <Lock class="size-2.5" />
            Soon
        </span>
    </div>

    <!-- Grouped Button (with + action) -->
    <div
        v-else-if="item.createHref"
        class="flex w-full overflow-hidden rounded-lg"
    >
        <Link
            :href="item.href"
            :class="[
                'flex flex-1 items-center gap-2 px-3 py-2.5 text-[13px] font-medium transition-all duration-200',
                'border-l-[3px]',
                active
                    ? 'border-accent bg-linear-to-r from-accent/25 via-accent/10 to-transparent font-semibold text-white'
                    : 'border-transparent bg-white/5 text-white/90 hover:bg-white/10',
            ]"
        >
            <component
                :is="item.icon"
                v-if="item.icon"
                :class="[
                    'size-4 shrink-0',
                    active ? 'text-accent' : 'text-white/70',
                ]"
            />
            <span class="truncate">{{ item.title }}</span>
        </Link>
        <Link
            :href="item.createHref"
            class="flex w-10 shrink-0 items-center justify-center bg-accent/70 text-white transition-colors hover:bg-accent"
            :title="`Create ${item.title}`"
        >
            <Plus class="size-4" />
        </Link>
    </div>

    <!-- Simple Link -->
    <Link
        v-else
        :href="item.href"
        :class="[
            'flex w-full items-center gap-2 rounded-r-lg px-3 py-2 text-[13px] font-medium transition-all duration-200',
            'border-l-[3px]',
            active
                ? 'border-accent bg-linear-to-r from-accent/20 to-transparent pl-2.5 font-semibold text-white'
                : 'border-transparent text-white/80 hover:bg-white/5 hover:text-white',
        ]"
    >
        <component
            :is="item.icon"
            v-if="item.icon"
            :class="[
                'size-4 shrink-0',
                active ? 'text-accent' : 'text-white/70',
            ]"
        />
        <span class="truncate">{{ item.title }}</span>
    </Link>
</template>
