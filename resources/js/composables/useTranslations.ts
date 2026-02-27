import { usePage } from '@inertiajs/vue3';
import { computed, type ComputedRef } from 'vue';

/**
 * Bengali numeral map
 */
const BENGALI_NUMERALS: Record<string, string> = {
    '0': '০',
    '1': '১',
    '2': '২',
    '3': '৩',
    '4': '৪',
    '5': '৫',
    '6': '৬',
    '7': '৭',
    '8': '৮',
    '9': '৯',
};

/**
 * Generic translation object type
 */
type TranslationValue = string | Record<string, any>;
type TranslationMap = Record<string, TranslationValue>;
type Replacements = Record<string, string | number>;

export function useTranslations() {
    const page = usePage();

    /**
     * Locale
     */
    const locale: ComputedRef<string> = computed(
        () => (page.props.locale as string) ?? 'en',
    );

    /**
     * Translations
     */
    const translations: ComputedRef<TranslationMap> = computed(
        () => (page.props.translations as TranslationMap) ?? {},
    );

    /**
     * i18n config
     */
    const i18nConfig: ComputedRef<Record<string, any>> = computed(
        () => (page.props.i18nConfig as Record<string, any>) ?? {},
    );

    /**
     * Bengali numerals toggle
     */
    const useBengaliNumerals: ComputedRef<boolean> = computed(() => {
        return (
            locale.value === 'bn' &&
            i18nConfig.value.useBengaliNumerals === true
        );
    });

    /**
     * RTL support
     */
    const isRtl: ComputedRef<boolean> = computed(() => {
        return ['ar', 'he', 'fa', 'ur'].includes(locale.value);
    });

    /**
     * Translation helper
     */
    const t = (key: string, replacements: Replacements = {}): string => {
        const keys = key.split('.');
        let value: any = translations.value;

        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                return key;
            }
        }

        if (typeof value !== 'string') {
            return key;
        }

        let result = value;

        for (const [placeholder, replacement] of Object.entries(replacements)) {
            result = result.replace(
                new RegExp(`:${placeholder}`, 'gi'),
                String(replacement),
            );
        }

        return result;
    };

    /**
     * Translation with pluralization
     */
    const tc = (
        key: string,
        count: number,
        replacements: Replacements = {},
    ): string => {
        const value = t(key, { count, ...replacements });

        if (!value.includes('|')) {
            return value;
        }

        const parts = value.split('|');

        // singular | plural
        if (parts.length === 2) {
            return count === 1 ? parts[0].trim() : parts[1].trim();
        }

        // Laravel-style ranges
        for (const part of parts) {
            const trimmed = part.trim();

            const exactMatch = trimmed.match(/^\{(\d+)\}\s*(.*)$/);
            if (exactMatch && Number(exactMatch[1]) === count) {
                return exactMatch[2].trim();
            }

            const rangeMatch = trimmed.match(/^\[(\d+),(\d+|\*)\]\s*(.*)$/);
            if (rangeMatch) {
                const min = Number(rangeMatch[1]);
                const max =
                    rangeMatch[2] === '*' ? Infinity : Number(rangeMatch[2]);

                if (count >= min && count <= max) {
                    return rangeMatch[3].trim();
                }
            }
        }

        return parts[parts.length - 1].trim();
    };

    /**
     * Key existence check
     */
    const has = (key: string): boolean => {
        const keys = key.split('.');
        let value: any = translations.value;

        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                return false;
            }
        }

        return typeof value === 'string';
    };

    /**
     * Namespace fetch
     */
    const getNamespace = (namespace: string): Record<string, any> => {
        return (translations.value[namespace] as Record<string, any>) ?? {};
    };

    /**
     * Bengali digit conversion
     */
    const toBengaliNumerals = (num: number | string): string => {
        if (!useBengaliNumerals.value) {
            return String(num);
        }

        return String(num).replace(/[0-9]/g, (d) => BENGALI_NUMERALS[d] ?? d);
    };

    /**
     * Number formatting
     */
    const formatNumber = (num: number, decimals = 2): string => {
        const formatted = Number(num).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });

        return useBengaliNumerals.value
            ? toBengaliNumerals(formatted)
            : formatted;
    };

    /**
     * Currency formatting
     */
    const formatCurrency = (
        amount: number,
        symbol = '৳',
        decimals = 2,
    ): string => {
        return `${symbol} ${formatNumber(amount, decimals)}`;
    };

    return {
        t,
        tc,
        has,
        locale,
        isRtl,
        translations,
        getNamespace,
        useBengaliNumerals,
        toBengaliNumerals,
        formatNumber,
        formatCurrency,
    };
}
