import { usePage } from '@inertiajs/vue3';
import { computed, type ComputedRef } from 'vue';

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

type FeaturesMap = Record<string, boolean | number | string | undefined>;

interface Features extends FeaturesMap {
    // Add your application-specific feature flags here
    // Example:
    // notifications?: boolean;
    // analytics?: boolean;
}

/*
|--------------------------------------------------------------------------
| Composable
|--------------------------------------------------------------------------
*/

export function useFeatures() {
    const page = usePage();

    const features: ComputedRef<Features> = computed(
        () => (page.props.features as Features) ?? {},
    );

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
    */

    /**
     * Check if a specific feature is enabled
     */
    const isEnabled = (feature: keyof Features | string): boolean => {
        return features.value[feature as keyof Features] === true;
    };

    /**
     * Get the value of a feature flag
     */
    const get = <T = unknown>(
        feature: keyof Features | string,
        defaultValue?: T,
    ): T => {
        return (features.value[feature as keyof Features] as T) ?? (defaultValue as T);
    };

    /**
     * Get all feature flags
     */
    const all = (): Features => {
        return features.value;
    };

    return {
        // Utilities
        isEnabled,
        get,
        all,

        // Raw features
        features,
    };
}
