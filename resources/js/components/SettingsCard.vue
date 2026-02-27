<template>
    <component
        :is="disabled ? 'div' : Link"
        :href="disabled ? undefined : href"
        class="group relative rounded-xl border bg-white/60 p-4 shadow-sm shadow-gray-100/50 transition-all duration-300"
        :class="cardClasses"
    >
        <!-- Coming Soon Badge -->
        <div
            v-if="comingSoon && !disabled"
            class="absolute top-2.5 right-2.5 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold tracking-wide text-amber-700 uppercase"
        >
            Soon
        </div>

        <!-- Disabled Overlay -->
        <div
            v-if="disabled"
            class="absolute inset-0 z-10 rounded-xl bg-gray-50/70"
        ></div>

        <!-- Icon -->
        <div
            class="mb-3 flex h-9 w-9 items-center justify-center rounded-lg transition-all duration-300"
            :class="iconContainerClasses"
        >
            <component
                :is="iconComponent"
                class="h-4 w-4 transition-colors duration-300"
                :class="iconClasses"
            />
        </div>

        <!-- Content -->
        <div>
            <h3
                class="mb-0.5 text-sm font-semibold text-gray-900"
                :class="{ 'text-gray-400': disabled }"
            >
                {{ title }}
            </h3>
            <p
                class="line-clamp-2 text-xs leading-relaxed text-gray-500"
                :class="{ 'text-gray-300': disabled }"
            >
                {{ description }}
            </p>
        </div>

        <!-- Arrow -->
        <div
            v-if="!disabled"
            class="absolute top-1/2 right-3 -translate-y-1/2 opacity-0 transition-all duration-300 group-hover:translate-x-1 group-hover:opacity-100"
        >
            <ChevronRight class="h-4 w-4" :class="arrowClasses" />
        </div>
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
    description: string;
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

const colorClasses = {
    blue: {
        container: 'bg-blue-100 group-hover:bg-blue-500',
        icon: 'text-blue-600 group-hover:text-white',
        arrow: 'text-blue-500',
        border: 'border-gray-200 hover:border-blue-300',
    },
    emerald: {
        container: 'bg-emerald-100 group-hover:bg-emerald-500',
        icon: 'text-emerald-600 group-hover:text-white',
        arrow: 'text-emerald-500',
        border: 'border-gray-200 hover:border-emerald-300',
    },
    purple: {
        container: 'bg-purple-100 group-hover:bg-purple-500',
        icon: 'text-purple-600 group-hover:text-white',
        arrow: 'text-purple-500',
        border: 'border-gray-200 hover:border-purple-300',
    },
    orange: {
        container: 'bg-orange-100 group-hover:bg-orange-500',
        icon: 'text-orange-600 group-hover:text-white',
        arrow: 'text-orange-500',
        border: 'border-gray-200 hover:border-orange-300',
    },
    rose: {
        container: 'bg-rose-100 group-hover:bg-rose-500',
        icon: 'text-rose-600 group-hover:text-white',
        arrow: 'text-rose-500',
        border: 'border-gray-200 hover:border-rose-300',
    },
    amber: {
        container: 'bg-amber-100 group-hover:bg-amber-500',
        icon: 'text-amber-600 group-hover:text-white',
        arrow: 'text-amber-500',
        border: 'border-gray-200 hover:border-amber-300',
    },
    gray: {
        container: 'bg-gray-100 group-hover:bg-gray-500',
        icon: 'text-gray-600 group-hover:text-white',
        arrow: 'text-gray-500',
        border: 'border-gray-200 hover:border-gray-300',
    },
};

const cardClasses = computed(() => {
    if (props.disabled) {
        return 'border-gray-200 cursor-not-allowed';
    }
    return colorClasses[props.color].border;
});

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

const arrowClasses = computed(() => colorClasses[props.color].arrow);
</script>
