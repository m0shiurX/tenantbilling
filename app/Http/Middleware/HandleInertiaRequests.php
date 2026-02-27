<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'locale' => app()->getLocale(),
            'availableLocales' => config('app.available_languages', []),
            'translations' => fn () => $this->getTranslations(),
            'i18nConfig' => fn () => $this->getI18nConfig(),
            // 'features' => fn() => \App\Helpers\Features::all(),
        ];
    }

    /**
     * Load all translation files for the current locale with caching.
     *
     * @return array<string, mixed>
     */
    protected function getTranslations(): array
    {
        $locale = app()->getLocale();

        // Cache translations for 24 hours in production, skip cache in local
        if (app()->environment('local')) {
            return $this->loadTranslationsFromFiles($locale);
        }

        return Cache::remember("translations.{$locale}", now()->addHours(24), function () use ($locale) {
            return $this->loadTranslationsFromFiles($locale);
        });
    }

    /**
     * Load translations from files (used by cache).
     *
     * @return array<string, mixed>
     */
    protected function loadTranslationsFromFiles(string $locale): array
    {
        $langPath = lang_path($locale);
        $translations = [];

        // Load PHP translation files (e.g., common.php, nav.php)
        if (File::isDirectory($langPath)) {
            foreach (File::files($langPath) as $file) {
                if ($file->getExtension() === 'php') {
                    $key = $file->getFilenameWithoutExtension();
                    $translations[$key] = require $file->getPathname();
                }
            }
        }

        // Also load JSON translations if they exist (e.g., lang/bn.json)
        $jsonPath = lang_path("{$locale}.json");
        if (File::exists($jsonPath)) {
            $jsonTranslations = json_decode(File::get($jsonPath), true);
            if (is_array($jsonTranslations)) {
                $translations = array_merge($translations, $jsonTranslations);
            }
        }

        return $translations;
    }

    /**
     * Get i18n configuration for the frontend.
     *
     * @return array<string, mixed>
     */
    protected function getI18nConfig(): array
    {
        $locale = app()->getLocale();
        $localeConfig = config("i18n.locales.{$locale}", []);

        return [
            'useBengaliNumerals' => config('i18n.bengali_numerals.enabled', false)
                && ($localeConfig['use_native_numerals'] ?? false),
            'dateFormat' => $localeConfig['date_format'] ?? 'M d, Y',
            'datetimeFormat' => $localeConfig['datetime_format'] ?? 'M d, Y h:i A',
            'currencyPosition' => $localeConfig['currency_position'] ?? 'before',
            'isRtl' => $localeConfig['rtl'] ?? false,
        ];
    }
}
