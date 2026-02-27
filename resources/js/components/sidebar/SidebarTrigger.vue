<script setup lang="ts">
import { PanelLeftClose, PanelLeftOpen, Menu } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useSidebar } from '@/composables/useSidebar';

const { isCollapsed, toggle, toggleMobile } = useSidebar();
</script>

<template>
    <!-- Mobile Toggle -->
    <Button
        variant="ghost"
        size="icon"
        class="h-8 w-8 lg:hidden"
        @click="toggleMobile"
    >
        <Menu class="h-5 w-5" />
        <span class="sr-only">Toggle mobile menu</span>
    </Button>

    <!-- Desktop Toggle -->
    <Button
        variant="ghost"
        size="icon"
        class="hidden h-8 w-8 lg:flex"
        @click="toggle"
    >
        <PanelLeftOpen v-if="isCollapsed" class="h-5 w-5" />
        <PanelLeftClose v-else class="h-5 w-5" />
        <span class="sr-only">Toggle sidebar</span>
    </Button>
</template>
