<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    title: string;
    subtitle?: string;
    showBack?: boolean;
    backHref?: string;
}

withDefaults(defineProps<Props>(), {
    showBack: false,
    backHref: '/',
});
</script>

<template>
    <header class="mb-6">
        <div class="flex items-center gap-3">
            <Link
                v-if="showBack"
                :href="backHref"
                class="bg-muted/50 hover:bg-muted inline-flex h-9 w-9 items-center justify-center rounded-full transition-colors"
            >
                <ArrowLeft class="text-muted-foreground h-5 w-5" />
                <span class="sr-only">Go back</span>
            </Link>
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ title }}
                </h1>
                <p
                    v-if="subtitle"
                    class="text-muted-foreground text-sm md:hidden"
                >
                    {{ subtitle }}
                </p>
            </div>
        </div>
    </header>
</template>
