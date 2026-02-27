<template>
    <header
        class="sticky top-0 z-1030 flex h-14 items-center border-b border-primary-light bg-white px-3 text-secondary shadow-sm print:hidden"
    >
        <!-- Mobile Menu Toggle -->
        <button
            class="me-auto lg:hidden"
            type="button"
            aria-label="Toggle mobile menu"
            @click="handleMobileToggle"
        >
            <svg
                class="size-7 text-slate-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    d="M3 12h12M3 6h18M3 18h18"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </button>

        <!-- Desktop Sidebar Toggle -->
        <button
            class="relative z-1050 ms-3 hidden lg:block"
            type="button"
            aria-label="Toggle sidebar"
            @click="toggle"
        >
            <div class="relative size-5">
                <span
                    class="absolute top-0.5 block h-0.5 w-full bg-secondary transition-all duration-300"
                />
                <span
                    class="absolute top-1/2 block h-0.5 w-full max-w-[50%] bg-secondary transition-all duration-300"
                />
                <span
                    class="absolute bottom-0 block h-0.5 w-full bg-secondary transition-all duration-300"
                />
            </div>
        </button>

        <!-- Page Title Slot -->
        <slot name="page-title" />

        <!-- Right Side Actions -->
        <ul class="m-0 ml-auto flex list-none items-center gap-2 p-0 sm:gap-3">
            <!-- Fullscreen Button (Desktop only) -->
            <li class="hidden list-none md:block">
                <button
                    id="fullscreenButton"
                    tabindex="0"
                    @click="toggleFullscreen"
                    class="btn flex h-9 min-h-9 w-9 items-center justify-center rounded-md bg-slate-300 p-0 dash-ring transition-colors hover:bg-slate-200"
                    aria-label="Toggle fullscreen"
                >
                    <FullscreenIcon class-name="size-5" aria-hidden="true" />
                </button>
            </li>

            <!-- User Menu (with Language Switcher) -->
            <li class="list-none">
                <div class="relative" ref="userMenuRef">
                    <button
                        type="button"
                        class="btn btn-circle h-9 min-h-9 w-9 bg-slate-300 dash-ring transition-colors hover:bg-slate-200"
                        aria-label="User menu"
                        :aria-expanded="isUserMenuOpen"
                        @click="toggleUserMenu"
                    >
                        <UserIcon class-name="size-5" aria-hidden="true" />
                    </button>
                    <ul
                        v-show="isUserMenuOpen"
                        class="menu absolute right-0 z-50 mt-2 w-52 rounded-lg border border-gray-200 bg-white p-2 shadow-lg"
                    >
                        <!-- Profile -->
                        <li>
                            <Link
                                :href="profileEditUrl"
                                prefetch
                                @click="closeUserMenu"
                                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900 hover:no-underline"
                            >
                                <UserIcon class-name="size-4 text-gray-500" />
                                {{ t('auth.my_profile') }}
                            </Link>
                        </li>

                        <!-- Settings -->
                        <li>
                            <Link
                                :href="settingsUrl"
                                prefetch
                                @click="closeUserMenu"
                                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900 hover:no-underline"
                            >
                                <SettingsIcon
                                    class-name="size-4 text-gray-500"
                                />
                                {{ t('auth.settings') }}
                            </Link>
                        </li>

                        <!-- Language Toggle (if multiple languages) -->
                        <li
                            v-if="
                                availableLanguages &&
                                Object.keys(availableLanguages).length > 1
                            "
                        >
                            <Link
                                :href="toggleLanguageUrl"
                                @click="closeUserMenu"
                                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900 hover:no-underline"
                            >
                                <LanguageIcon
                                    class-name="size-4 text-gray-500"
                                />
                                <span class="flex-1 text-left">{{
                                    t('nav.language')
                                }}</span>
                                <span class="flex items-center gap-1.5 text-xs">
                                    <span
                                        class="rounded px-1.5 py-0.5 font-medium"
                                        :class="
                                            currentLocale === 'en'
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-gray-100 text-gray-500'
                                        "
                                    >
                                        EN
                                    </span>
                                    <svg
                                        class="size-3.5 text-gray-400"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span
                                        class="rounded px-1.5 py-0.5 font-medium"
                                        :class="
                                            currentLocale === 'bn'
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-gray-100 text-gray-500'
                                        "
                                    >
                                        BN
                                    </span>
                                </span>
                            </Link>
                        </li>

                        <li class="my-1.5 border-t border-gray-200" />

                        <!-- Logout -->
                        <li>
                            <button
                                @click="
                                    () => {
                                        closeUserMenu();
                                        handleLogout();
                                    }
                                "
                                class="flex w-full cursor-pointer items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm text-red-600 transition-colors hover:bg-red-50"
                            >
                                <LogoutIcon class-name="size-4" />
                                {{ t('auth.logout') }}
                            </button>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </header>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import {
    FullscreenIcon,
    UserIcon,
    SettingsIcon,
    LogoutIcon,
    LanguageIcon,
} from '@/components/Icons';
import { useSidebar } from '@/composables/useSidebar';
import { useTranslations } from '@/composables/useTranslations';
import { logout } from '@/routes';
import { index as accountIndex } from '@/routes/account';
import { index as settingsIndex } from '@/routes/settings';
const { toggle, toggleMobile } = useSidebar();
const { t } = useTranslations();
const page = usePage();

const isUserMenuOpen = ref(false);
const userMenuRef = ref(null);

// Wrapper function for mobile toggle with debug
function handleMobileToggle() {
    console.log('handleMobileToggle clicked');
    toggleMobile();
}

// Props for language configuration
const props = defineProps({
    availableLanguages: {
        type: Object,
        default: () => ({}),
    },
    currentLocale: {
        type: String,
        default: 'en',
    },
});

const settingsUrl = computed(() => settingsIndex().url);
const profileEditUrl = computed(() => accountIndex().url);
const logoutUrl = computed(() => logout().url);

// Get the opposite language for toggle
const toggleLanguageUrl = computed(() => {
    const locales = Object.keys(props.availableLanguages);
    const otherLocale =
        locales.find((locale) => locale !== props.currentLocale) || locales[0];
    const url = new URL(window.location.href);
    url.searchParams.set('lang', otherLocale);
    return url.toString();
});

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const closeUserMenu = () => {
    isUserMenuOpen.value = false;
};

const handleUserMenuClickOutside = (event) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
        closeUserMenu();
    }
};

onMounted(() => {
    document.addEventListener('click', handleUserMenuClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleUserMenuClickOutside);
});

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

function handleLogout() {
    // Use Inertia router with CSRF token from page props (always fresh after session regeneration)
    router.post(logoutUrl.value, {
        _token: page.props.csrf_token,
    });
}
</script>
