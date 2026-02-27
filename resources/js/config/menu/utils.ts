import { resolveIcon } from '@/lib/resolveIcon';
import type { RouteQueryOptions } from '@/wayfinder';
import type {
    WayfinderRoute,
    MenuItem,
    MainSection,
    BottomItem,
    ResolvedMenuItem,
    ResolvedMainSection,
    ResolvedBottomItem,
} from './types';

/**
 * Resolve a Wayfinder route to a URL string
 */
export function resolveMenuRoute(
    route?: WayfinderRoute,
    options?: RouteQueryOptions,
): string | undefined {
    if (!route) {
        return undefined;
    }

    return route(options).url;
}

/**
 * Get URL from a route, defaulting to '#' if undefined
 */
export function urlOf(route?: WayfinderRoute): string {
    return resolveMenuRoute(route) ?? '#';
}

/**
 * Check if the current route matches any of the given patterns
 *
 * Supports:
 * - Exact match: '/dashboard'
 * - Wildcard suffix: '/settings/*' or '/settings/.*'
 * - Prefix match: '/settings' matches '/settings/profile'
 */
export function matchesPattern(
    currentRoute: string,
    patterns: readonly string[] = [],
): boolean {
    if (!currentRoute || patterns.length === 0) {
        return false;
    }

    return patterns.some((pattern) => {
        // Wildcard pattern: '/admin/products.*'
        if (pattern.endsWith('.*')) {
            return currentRoute.startsWith(pattern.slice(0, -2));
        }

        // Wildcard pattern: '/admin/products*'
        if (pattern.endsWith('*')) {
            return currentRoute.startsWith(pattern.slice(0, -1));
        }

        // Exact match or prefix match
        return (
            currentRoute === pattern || currentRoute.startsWith(pattern + '/')
        );
    });
}

/**
 * Find the active section based on the current route
 * Checks both main sections and bottom items
 */
export function findActiveMainSection(
    currentRoute: string,
    sections: readonly MainSection[],
    bottomItems: readonly BottomItem[] = [],
): string {
    // Check main sections first
    for (const section of sections) {
        if (matchesPattern(currentRoute, section.activePatterns)) {
            return section.id;
        }
    }

    // Check bottom items (settings, etc.)
    for (const item of bottomItems) {
        if (
            item.activePatterns &&
            matchesPattern(currentRoute, item.activePatterns)
        ) {
            return item.id;
        }
    }

    return 'dashboard';
}

/**
 * Translate a single menu item
 */
export function translateMenuItem(
    item: MenuItem,
    t: (key: string) => string,
): ResolvedMenuItem {
    return {
        title: t(item.titleKey),
        subtitle: item.subtitleKey ? t(item.subtitleKey) : undefined,
        href: urlOf(item.route),
        createHref: item.createRoute ? urlOf(item.createRoute) : undefined,
        icon: resolveIcon(item.icon),
        activePatterns: item.activePatterns,
        variant: item.variant,
    };
}

/**
 * Translate an array of menu items
 */
export function translateMenuItems(
    items: MenuItem[],
    t: (key: string) => string,
): ResolvedMenuItem[] {
    return items.map((item) => translateMenuItem(item, t));
}

/**
 * Resolve a main section with translation and icon
 */
export function resolveMainSection(
    section: MainSection,
    t: (key: string) => string,
): ResolvedMainSection {
    return {
        id: section.id,
        title: t(section.titleKey),
        href: section.route ? urlOf(section.route) : undefined,
        iconComponent: resolveIcon(section.icon),
        activePatterns: section.activePatterns,
    };
}

/**
 * Resolve all main sections
 */
export function resolveMainSections(
    sections: readonly MainSection[],
    t: (key: string) => string,
): ResolvedMainSection[] {
    return sections.map((section) => resolveMainSection(section, t));
}

/**
 * Resolve a bottom item with translation and icon
 */
export function resolveBottomItem(
    item: BottomItem,
    t: (key: string) => string,
): ResolvedBottomItem {
    return {
        id: item.id,
        title: t(item.titleKey),
        href: item.route ? urlOf(item.route) : undefined,
        iconComponent: resolveIcon(item.icon),
        action: item.action,
        modalId: item.modalId,
        activePatterns: item.activePatterns,
        directLink: item.directLink,
    };
}

/**
 * Resolve all bottom items
 */
export function resolveBottomItems(
    items: readonly BottomItem[],
    t: (key: string) => string,
): ResolvedBottomItem[] {
    return items.map((item) => resolveBottomItem(item, t));
}
