import { ref, watch, type Ref } from 'vue';

const STORAGE_KEY = 'sidebar-collapsed';
const ACTIVE_MENU_KEY = 'sidebar-active-menu';

/**
 * Helpers
 */
const hasLocalStorage = (): boolean =>
    typeof window !== 'undefined' && typeof localStorage !== 'undefined';

/**
 * Shared state (singleton)
 */
const isCollapsed: Ref<boolean> = ref(
    hasLocalStorage() ? localStorage.getItem(STORAGE_KEY) === 'true' : false,
);

const isMobileOpen: Ref<boolean> = ref(false);

// Prevent auto-close on mobile navigation
const keepMobileOpen: Ref<boolean> = ref(false);

const activeMenu: Ref<string> = ref(
    hasLocalStorage()
        ? (localStorage.getItem(ACTIVE_MENU_KEY) ?? 'dashboard')
        : 'dashboard',
);

/**
 * Persist state
 */
if (hasLocalStorage()) {
    watch(isCollapsed, (value: boolean) => {
        localStorage.setItem(STORAGE_KEY, String(value));
    });

    watch(activeMenu, (value: string) => {
        localStorage.setItem(ACTIVE_MENU_KEY, value);
    });
}

/**
 * Sidebar composable
 */
export function useSidebar() {
    const toggle = (): void => {
        isCollapsed.value = !isCollapsed.value;
    };

    const collapse = (): void => {
        isCollapsed.value = true;
    };

    const expand = (): void => {
        isCollapsed.value = false;
    };

    const toggleMobile = (): void => {
        isMobileOpen.value = !isMobileOpen.value;
    };

    const openMobile = (): void => {
        isMobileOpen.value = true;
    };

    const closeMobile = (): void => {
        isMobileOpen.value = false;
    };

    const setActiveMenu = (menuId: string): void => {
        activeMenu.value = menuId;
    };

    const setKeepMobileOpen = (value: boolean): void => {
        keepMobileOpen.value = value;
    };

    const shouldAutoClose = (): boolean => {
        if (keepMobileOpen.value) {
            keepMobileOpen.value = false;
            return false;
        }
        return true;
    };

    return {
        // State
        isCollapsed,
        isMobileOpen,
        activeMenu,
        keepMobileOpen,

        // Actions
        toggle,
        collapse,
        expand,
        toggleMobile,
        openMobile,
        closeMobile,
        setActiveMenu,
        setKeepMobileOpen,
        shouldAutoClose,
    };
}
