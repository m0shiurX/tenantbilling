<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import Header from '@/components/header/Header.vue';
import Sidebar from '@/components/sidebar/Sidebar.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogClose,
} from '@/components/ui/dialog';
import { useSidebar } from '@/composables/useSidebar';

const page = usePage();
const { isCollapsed, isMobileOpen, closeMobile } = useSidebar();

// Get language config from shared data
const availableLanguages = computed(() => {
    return page.props.availableLanguages || {};
});

const currentLocale = computed(() => {
    return page.props.locale || 'en';
});

// Content margin based on sidebar state
const contentMarginClass = computed(() => {
    // On mobile, no margin. On desktop, margin based on collapsed state
    if (isCollapsed.value) {
        return 'ml-0 lg:ml-20';
    }
    return 'ml-0 lg:ml-[300px]';
});

const isHelpOpen = ref(false);

const openHelpModal = () => {
    isHelpOpen.value = true;
};

onMounted(() => {
    window.addEventListener('open-help-modal', openHelpModal as EventListener);
});

onUnmounted(() => {
    window.removeEventListener(
        'open-help-modal',
        openHelpModal as EventListener,
    );
});
</script>

<template>
    <div class="flex min-h-screen flex-row">
        <!-- Mobile Backdrop -->
        <div
            v-if="isMobileOpen"
            class="fixed inset-0 z-1025 bg-black/50 lg:hidden"
            @click="closeMobile"
        ></div>

        <Sidebar />
        <div
            class="flex min-w-0 flex-1 flex-col transition-all duration-300"
            :class="contentMarginClass"
        >
            <Header
                :available-languages="availableLanguages"
                :current-locale="currentLocale"
            >
                <template #page-title>
                    <slot name="page-title" />
                </template>
            </Header>
            <!--  Harmony - subtle brand-cohesive background -->
            <!-- bg-linear-to-bl from-slate-200 via-slate-300 to-slate-200 -->
            <div
                class="flex grow flex-col bg-primary-lighter print:border-0 print:bg-white print:outline-0"
            >
                <main
                    class="mx-auto w-full grow px-4 py-6 lg:px-8 sm:px-6 print:mt-0 print:border-0 print:bg-none print:p-0 print:outline-0"
                >
                    <slot />
                </main>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <Dialog v-model:open="isHelpOpen">
        <DialogContent class="max-w-md bg-white">
            <DialogHeader>
                <DialogTitle>Do you have any question?</DialogTitle>
                <DialogDescription class="text-muted-foreground text-sm">
                    Feel free to ask any query at moshiur@ryzan.co <br />
                    Or call me at
                    <a
                        class="font-bold text-emerald-800"
                        href="tel:+8801625292000"
                    >
                        +880 1625 292 000
                    </a>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <DialogClose as-child>
                    <button type="button" class="btn btn-sm">Close</button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
