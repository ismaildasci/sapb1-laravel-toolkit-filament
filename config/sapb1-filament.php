<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the SAP B1 admin panel appears in Filament navigation.
    |
    */

    'navigation' => [
        'group' => env('SAPB1_FILAMENT_NAV_GROUP', 'SAP B1'),
        'icon' => env('SAPB1_FILAMENT_NAV_ICON', 'heroicon-o-building-office'),
        'sort' => env('SAPB1_FILAMENT_NAV_SORT', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features in the admin panel.
    | Set to false to hide features from the navigation and dashboard.
    |
    */

    'features' => [
        'dashboard' => env('SAPB1_FILAMENT_DASHBOARD', true),
        'audit' => env('SAPB1_FILAMENT_AUDIT', true),
        'sync' => env('SAPB1_FILAMENT_SYNC', true),
        'cache' => env('SAPB1_FILAMENT_CACHE', true),
        'multi_tenant' => env('SAPB1_FILAMENT_MULTI_TENANT', true),
        'change_tracking' => env('SAPB1_FILAMENT_CHANGE_TRACKING', true),
        'settings' => env('SAPB1_FILAMENT_SETTINGS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Log Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the audit log viewer behavior.
    |
    */

    'audit' => [
        'per_page' => env('SAPB1_FILAMENT_AUDIT_PER_PAGE', 25),
        'max_export' => env('SAPB1_FILAMENT_AUDIT_MAX_EXPORT', 10000),
        'diff_mode' => env('SAPB1_FILAMENT_AUDIT_DIFF_MODE', 'side-by-side'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sync Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the sync status dashboard behavior.
    |
    */

    'sync' => [
        'poll_interval' => env('SAPB1_FILAMENT_SYNC_POLL_INTERVAL', 5),
        'allow_manual_sync' => env('SAPB1_FILAMENT_SYNC_ALLOW_MANUAL', true),
        'show_history' => env('SAPB1_FILAMENT_SYNC_SHOW_HISTORY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    |
    | Configure individual dashboard widgets.
    |
    */

    'widgets' => [
        'health' => [
            'enabled' => true,
            'refresh_interval' => 30,
        ],
        'audit_activity' => [
            'enabled' => true,
            'limit' => 10,
        ],
        'sync_overview' => [
            'enabled' => true,
            'refresh_interval' => 10,
        ],
        'cache_stats' => [
            'enabled' => true,
        ],
        'change_tracking' => [
            'enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Configure authorization for the admin panel.
    |
    */

    'authorization' => [
        'gate' => env('SAPB1_FILAMENT_AUTH_GATE', 'sapb1-admin'),
        'super_admin_role' => env('SAPB1_FILAMENT_SUPER_ADMIN_ROLE', 'super-admin'),
    ],
];
