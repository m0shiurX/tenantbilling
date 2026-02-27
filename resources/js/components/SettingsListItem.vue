<template>
    <component
        :is="disabled ? 'div' : Link"
        :href="disabled ? undefined : href"
        class="relative flex items-center gap-3 px-3 py-2.5 transition-colors"
        :class="
            disabled ? 'cursor-not-allowed opacity-50' : 'active:bg-gray-100'
        "
    >
        <!-- Icon -->
        <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
            :class="iconContainerClasses"
        >
            <component
                :is="iconComponent"
                class="h-4 w-4"
                :class="iconClasses"
            />
        </div>

        <!-- Title -->
        <span
            class="flex-1 truncate text-sm font-medium text-gray-900"
            :class="{ 'text-gray-400': disabled }"
        >
            {{ title }}
        </span>

        <!-- Coming Soon Badge -->
        <span
            v-if="comingSoon"
            class="rounded bg-amber-100 px-1.5 py-0.5 text-[9px] font-semibold tracking-wide text-amber-600 uppercase"
        >
            Soon
        </span>

        <!-- Arrow -->
        <ChevronRight v-if="!disabled" class="h-4 w-4 shrink-0 text-gray-400" />
    </component>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { computed, type Component } from 'vue';
import {
    BuildingIcon,
    ReceiptIcon,
    RulerIcon,
    CreditCardIcon,
    FolderIcon,
    UsersIcon,
    ShieldIcon,
    KeyIcon,
    DatabaseIcon,
    ClipboardIcon,
    BellIcon,
    CalculatorIcon,
    OfficeIcon,
    SettingsIcon,
    BarcodeIcon,
} from '@/components/Icons';

const iconMap: Record<string, Component> = {
    building: BuildingIcon,
    document: ReceiptIcon,
    ruler: RulerIcon,
    'credit-card': CreditCardIcon,
    folder: FolderIcon,
    users: UsersIcon,
    shield: ShieldIcon,
    key: KeyIcon,
    database: DatabaseIcon,
    clipboard: ClipboardIcon,
    bell: BellIcon,
    calculator: CalculatorIcon,
    office: OfficeIcon,
    settings: SettingsIcon,
    barcode: BarcodeIcon,
};

interface Props {
    title: string;
    href: string;
    icon?: string;
    color?:
        | 'gray'
        | 'blue'
        | 'emerald'
        | 'purple'
        | 'orange'
        | 'rose'
        | 'amber';
    comingSoon?: boolean;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    icon: 'settings',
    color: 'gray',
    comingSoon: false,
    disabled: false,
});

const iconComponent = computed(() => iconMap[props.icon] || SettingsIcon);

const colorClasses: Record<string, { container: string; icon: string }> = {
    blue: {
        container: 'bg-blue-100',
        icon: 'text-blue-600',
    },
    emerald: {
        container: 'bg-emerald-100',
        icon: 'text-emerald-600',
    },
    purple: {
        container: 'bg-purple-100',
        icon: 'text-purple-600',
    },
    orange: {
        container: 'bg-orange-100',
        icon: 'text-orange-600',
    },
    rose: {
        container: 'bg-rose-100',
        icon: 'text-rose-600',
    },
    amber: {
        container: 'bg-amber-100',
        icon: 'text-amber-600',
    },
    gray: {
        container: 'bg-gray-100',
        icon: 'text-gray-600',
    },
};

const iconContainerClasses = computed(() => {
    if (props.disabled) {
        return 'bg-gray-100';
    }
    return colorClasses[props.color].container;
});

const iconClasses = computed(() => {
    if (props.disabled) {
        return 'text-gray-400';
    }
    return colorClasses[props.color].icon;
});
</script>
