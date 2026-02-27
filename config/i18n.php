<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale for the application. This will be used when no
    | locale is specified.
    |
    */

    'default_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    |
    | A list of available locales for the application. Each locale can have
    | its own configuration for numerals, date format, and other settings.
    |
    */

    'locales' => [
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'rtl' => false,
            'use_native_numerals' => false,
            'date_format' => 'M d, Y',
            'datetime_format' => 'M d, Y h:i A',
            'currency_position' => 'before', // 'before' or 'after'
        ],
        'bn' => [
            'name' => 'Bengali',
            'native_name' => 'বাংলা',
            'rtl' => false,
            'use_native_numerals' => true, // Set to true to enable Bengali numerals
            'date_format' => 'd M, Y',
            'datetime_format' => 'd M, Y h:i A',
            'currency_position' => 'before',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bengali Numeral Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for Bengali numeral conversion. When enabled, numbers
    | will be displayed using Bengali numerals (০১২৩৪৫৬৭৮৯).
    |
    */

    'bengali_numerals' => [
        'enabled' => true, // Global toggle for Bengali numerals
        'map' => [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Language Key
    |--------------------------------------------------------------------------
    |
    | The session key used to store the user's preferred language.
    |
    */

    'session_key' => 'language',

    /*
    |--------------------------------------------------------------------------
    | Query Parameter
    |--------------------------------------------------------------------------
    |
    | The query parameter used to change the language via URL.
    |
    */

    'query_parameter' => 'lang',

];
