import type { Component } from 'vue';
import type { RouteDefinition, RouteQueryOptions } from '@/wayfinder';

/**
 * HTTP methods supported by Wayfinder routes
 */
type HttpMethod =
    | 'get'
    | 'post'
    | 'put'
    | 'patch'
    | 'delete'
    | 'head'
    | 'options';

/**
 * A Wayfinder route function that returns a RouteDefinition
 */
export type WayfinderRoute = (
    options?: RouteQueryOptions,
) => RouteDefinition<HttpMethod>;

/**
 * Feature flags that can be used to conditionally show menu items
 * Add your application-specific feature flags here
 */
export type FeatureKey = string;

/**
 * Business type for conditional menu items
 */
export type BusinessType = 'agro' | 'retail' | string;

/**
 * Visual variant for menu items
 */
export type MenuVariant = 'default' | 'success' | 'info' | 'warning' | 'danger';

/**
 * Base menu item configuration
 */
export interface MenuItem {
    /** Translation key for the title (e.g., 'nav.dashboard') */
    titleKey: string;
    /** Icon name from @/components/Icons */
    icon?: string;
    /** Wayfinder route function */
    route?: WayfinderRoute;
    /** Wayfinder route function for create action (shows + button) */
    createRoute?: WayfinderRoute;
    /** Direct href URL (alternative to route, for disabled/coming soon items) */
    href?: string;
    /** URL patterns to match for active state */
    activePatterns?: string[];
    /** Feature flag required to show this item */
    requiredFeature?: FeatureKey;
    /** Business type required to show this item */
    requiredBusinessType?: BusinessType;
    /** Visual variant for styling */
    variant?: MenuVariant;
    /** Translation key for subtitle */
    subtitleKey?: string;
    /** Whether the item is disabled (coming soon) */
    disabled?: boolean;
}

/**
 * A group of menu items with a title
 */
export interface MenuGroup {
    /** Translation key for the group title */
    titleKey: string;
    /** Feature flag required to show this group */
    requiredFeature?: FeatureKey;
    /** Menu items in this group */
    items: MenuItem[];
    /** Whether the group is collapsible */
    collapsible?: boolean;
}

/**
 * Detail menu shown when a main section is active
 */
export interface DetailMenu {
    /** Translation key for the menu title */
    titleKey: string;
    /** Groups of menu items */
    groups: MenuGroup[];
}

/**
 * Main section in the sidebar icon rail
 */
export interface MainSection {
    /** Unique identifier for the section */
    id: string;
    /** Translation key for the title */
    titleKey: string;
    /** Icon name from @/components/Icons */
    icon: string;
    /** Wayfinder route function */
    route?: WayfinderRoute;
    /** Direct href URL (alternative to route, for disabled/coming soon items) */
    href?: string;
    /** URL patterns to match for active state */
    activePatterns: string[];
    /** Feature flag required to show this section */
    requiredFeature?: FeatureKey;
    /** Whether the section is disabled (coming soon) */
    disabled?: boolean;
}

/**
 * Bottom item in the sidebar (settings, help, etc.)
 */
export interface BottomItem {
    /** Unique identifier */
    id: string;
    /** Translation key for the title */
    titleKey: string;
    /** Icon name from @/components/Icons */
    icon: string;
    /** Wayfinder route function */
    route?: WayfinderRoute;
    /** Direct href URL (alternative to route) */
    href?: string;
    /** Action type (e.g., 'modal') */
    action?: 'modal';
    /** Modal ID to open when clicked */
    modalId?: string;
    /** URL patterns to match for active state */
    activePatterns?: string[];
    /** If true, clicking navigates directly to the route instead of opening detail menu */
    directLink?: boolean;
}

/**
 * Complete menu configuration
 */
export interface MenuConfig {
    /** Main sections in the icon rail */
    mainSections: MainSection[];
    /** Detail menus for each section */
    detailMenus: Record<string, DetailMenu>;
    /** Bottom items in the sidebar */
    bottomItems: BottomItem[];
}

/**
 * Resolved menu item with translated title and resolved href
 */
export interface ResolvedMenuItem {
    id?: string;
    title: string;
    subtitle?: string;
    href: string;
    createHref?: string;
    icon?: Component;
    activePatterns?: string[];
    variant?: MenuVariant;
}

/**
 * Resolved main section with translated title and resolved href
 */
export interface ResolvedMainSection {
    id: string;
    title: string;
    href?: string;
    iconComponent?: Component;
    activePatterns: string[];
}

/**
 * Resolved bottom item with translated title and resolved href
 */
export interface ResolvedBottomItem {
    id: string;
    title: string;
    href?: string;
    iconComponent?: Component;
    action?: 'modal';
    modalId?: string;
    activePatterns?: string[];
    directLink?: boolean;
}
