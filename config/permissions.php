<?php

/**
 * Permission Registry
 *
 * This file defines all permissions available in the application.
 * Use `php artisan permissions:sync` to synchronize with the database.
 *
 * Structure:
 * 'feature_name' => [
 *     'group' => 'Human Readable Group Name',
 *     'abilities' => ['access', 'create', 'edit', 'show', 'delete'],
 * ]
 *
 * This generates permissions like: feature_name_access, feature_name_create, etc.
 *
 * For custom permission names, use the 'custom' key:
 * 'feature_name' => [
 *     'group' => 'Group Name',
 *     'custom' => ['special_action', 'another_action'],
 * ]
 *
 * This generates: feature_name_special_action, feature_name_another_action
 */

return [

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */

    'user_management' => [
        'group' => 'User Management',
        'abilities' => ['access'],
    ],

    'permission' => [
        'group' => 'Permissions',
        'abilities' => ['access', 'create', 'edit', 'show', 'delete'],
    ],

    'role' => [
        'group' => 'Roles',
        'abilities' => ['access', 'create', 'edit', 'show', 'delete'],
    ],

    'user' => [
        'group' => 'Users',
        'abilities' => ['access', 'create', 'edit', 'show', 'delete'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    'profile_password' => [
        'group' => 'Profile',
        'abilities' => ['edit'],
    ],

];
