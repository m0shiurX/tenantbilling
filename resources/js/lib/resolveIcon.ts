import type { Component } from 'vue';
import * as Icons from '@/components/Icons';

export const resolveIcon = (icon?: string): Component | undefined => {
    if (!icon) {
        return undefined;
    }

    return Icons[icon as keyof typeof Icons] as Component | undefined;
};
