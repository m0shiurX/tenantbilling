/**
 * Menu Configuration Module
 *
 * This module provides a centralized, type-safe menu configuration system
 * that integrates with Laravel Wayfinder for route generation.
 *
 * Usage:
 * ```ts
 * import { menuConfig, mainSections, findActiveMainSection } from '@/config/menu';
 * ```
 *
 * To add new menu items, edit `config.ts` and follow the inline documentation.
 */

// Types
export type {
    WayfinderRoute,
    FeatureKey,
    BusinessType,
    MenuVariant,
    MenuItem,
    MenuGroup,
    DetailMenu,
    MainSection,
    BottomItem,
    MenuConfig,
    ResolvedMenuItem,
    ResolvedMainSection,
    ResolvedBottomItem,
} from './types';

// Utilities
export {
    resolveMenuRoute,
    urlOf,
    matchesPattern,
    findActiveMainSection,
    translateMenuItem,
    translateMenuItems,
    resolveMainSection,
    resolveMainSections,
    resolveBottomItem,
    resolveBottomItems,
} from './utils';

// Configuration
export { menuConfig, mainSections, detailMenus, bottomItems } from './config';
