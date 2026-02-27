// Import Wayfinder routes
import { dashboard } from '@/routes';
import { home } from '@/routes';
import { index as settingsIndex } from '@/routes/settings';
import type { MenuConfig } from './types';

/**
 * Application Menu Configuration
 *
 * Navigation Structure:
 * - Main: Dashboard
 * - Settings: User Management, Roles, Permissions
 */
export const menuConfig: MenuConfig = {
    /**
     * Main sections displayed in the sidebar icon rail
     */
    mainSections: [
        // Main - Dashboard
        {
            id: 'main',
            titleKey: 'nav.main',
            icon: 'DashboardIcon',
            route: dashboard,
            activePatterns: ['/dashboard'],
        },
    ],

    /**
     * Detail menus shown when a main section is active
     */
    detailMenus: {
        // MAIN - Dashboard
        main: {
            titleKey: 'nav.main',
            groups: [
                {
                    titleKey: 'nav.overview',
                    items: [
                        {
                            titleKey: 'nav.dashboard',
                            route: dashboard,
                            icon: 'DashboardIcon',
                            activePatterns: ['/dashboard'],
                        },
                        {
                            titleKey: 'nav.home',
                            route: home,
                            icon: 'KanbanIcon',
                            activePatterns: ['/'],
                        },
                    ],
                },
            ],
        },
    },

    /**
     * Bottom items in the sidebar (always visible)
     */
    bottomItems: [
        {
            id: 'help',
            titleKey: 'nav.help',
            icon: 'QuestionIcon',
            action: 'modal',
            modalId: 'helpModal',
        },
        {
            id: 'settings',
            titleKey: 'nav.settings',
            icon: 'SettingsIcon',
            route: settingsIndex,
            href: '/settings',
            activePatterns: ['/settings*', '/account*'],
        },
    ],
};

// Named exports for convenience
export const { mainSections, detailMenus, bottomItems } = menuConfig;
