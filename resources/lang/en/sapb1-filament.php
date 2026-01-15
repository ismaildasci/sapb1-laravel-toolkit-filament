<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    'pages' => [
        'dashboard' => [
            'navigation_label' => 'Dashboard',
            'title' => 'SAP B1 Dashboard',
            'heading' => 'SAP Business One',
            'subheading' => 'System overview and monitoring',
        ],
        'settings' => [
            'navigation_label' => 'Settings',
            'title' => 'SAP B1 Settings',
            'heading' => 'Configuration Settings',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    */

    'widgets' => [
        'health' => [
            'sap_connection' => 'SAP Connection',
            'audit_system' => 'Audit System',
            'sync_system' => 'Sync System',
            'cache_system' => 'Cache System',
            'multi_tenant' => 'Multi-Tenant',
        ],
        'audit' => [
            'heading' => 'Recent Audit Activity',
            'entity' => 'Entity',
            'id' => 'ID',
            'event' => 'Event',
            'user' => 'User',
            'time' => 'Time',
        ],
        'sync' => [
            'heading' => 'Sync Status',
            'entity' => 'Entity',
            'status' => 'Status',
            'last_sync' => 'Last Sync',
            'records' => 'Records',
            'never' => 'Never',
            'sync_now' => 'Sync',
            'full_sync' => 'Full Sync',
            'full_sync_warning' => 'This will perform a full sync with delete detection. This may take a while.',
            'sync_success' => 'Sync completed',
            'full_sync_success' => 'Full sync completed',
            'sync_failed' => 'Sync failed',
        ],
        'cache' => [
            'status' => 'Cache Status',
            'enabled' => 'Enabled',
            'disabled' => 'Disabled',
            'store' => 'Cache Store',
            'ttl' => 'Default TTL',
            'entities' => 'Cached Entities',
            'entities_enabled' => 'entities with caching enabled',
            'flush_all' => 'Flush All Cache',
            'flush_confirm_title' => 'Flush Cache',
            'flush_confirm_description' => 'Are you sure you want to flush all SAP B1 toolkit cache?',
            'flush_success' => 'Cache flushed successfully',
            'flush_failed' => 'Failed to flush cache',
        ],
        'tracking' => [
            'active_watchers' => 'Active Watchers',
            'entities_monitored' => 'entities being monitored',
            'entities' => 'Watched Entities',
            'being_watched' => 'currently being tracked',
            'status' => 'Tracking Status',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'error' => 'Error',
            'polling_status' => 'polling status',
            'poll_now' => 'Poll Now',
            'poll_complete' => 'Polling complete',
            'poll_failed' => 'Polling failed',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    */

    'resources' => [
        'audit_log' => [
            'navigation_label' => 'Audit Logs',
            'model_label' => 'Audit Log',
            'plural_model_label' => 'Audit Logs',
            'sections' => [
                'details' => 'Details',
                'context' => 'Context',
                'changes' => 'Changes',
            ],
            'fields' => [
                'entity_type' => 'Entity Type',
                'entity_id' => 'Entity ID',
                'event' => 'Event',
                'user_id' => 'User ID',
                'tenant_id' => 'Tenant ID',
                'ip_address' => 'IP Address',
                'user_agent' => 'User Agent',
                'old_values' => 'Old Values',
                'new_values' => 'New Values',
                'created_at' => 'Created At',
            ],
            'filters' => [
                'entity_type' => 'Entity Type',
                'event' => 'Event',
                'from' => 'From',
                'until' => 'Until',
            ],
            'actions' => [
                'export' => 'Export',
            ],
        ],
        'sync_metadata' => [
            'navigation_label' => 'Sync Status',
            'model_label' => 'Sync Entity',
            'plural_model_label' => 'Sync Entities',
            'sections' => [
                'info' => 'Sync Information',
                'errors' => 'Error Details',
            ],
            'fields' => [
                'entity' => 'Entity',
                'table_name' => 'Table Name',
                'status' => 'Status',
                'synced_count' => 'Synced Records',
                'last_synced_at' => 'Last Synced',
                'last_full_sync_at' => 'Last Full Sync',
                'last_error' => 'Last Error',
                'has_error' => 'Has Error',
            ],
            'filters' => [
                'status' => 'Status',
            ],
            'actions' => [
                'sync' => 'Sync',
                'full_sync' => 'Full Sync',
                'reset' => 'Reset',
                'sync_all' => 'Sync All',
                'bulk_sync' => 'Sync Selected',
            ],
            'never' => 'Never',
            'full_sync_warning' => 'This will perform a full sync with delete detection. This operation may take a while for large datasets.',
            'reset_warning' => 'This will reset all sync metadata for this entity. The next sync will be treated as a fresh sync.',
            'notifications' => [
                'sync_success' => 'Sync completed successfully',
                'full_sync_success' => 'Full sync completed successfully',
                'sync_failed' => 'Sync failed',
                'reset_success' => 'Metadata reset successfully',
                'sync_all_success' => 'All entities synced successfully',
                'bulk_sync_complete' => 'Bulk sync completed',
            ],
        ],
        'tenant' => [
            'navigation_label' => 'Tenants',
            'model_label' => 'Tenant',
            'plural_model_label' => 'Tenants',
            'sections' => [
                'info' => 'Tenant Information',
                'connection' => 'SAP Connection',
            ],
            'fields' => [
                'id' => 'Tenant ID',
                'name' => 'Name',
                'sap_url' => 'SAP URL',
                'sap_database' => 'Database',
                'sap_username' => 'Username',
                'sap_password' => 'Password',
                'is_current' => 'Current',
                'status' => 'Status',
            ],
            'actions' => [
                'switch' => 'Switch',
                'test' => 'Test',
                'create' => 'Add Tenant',
            ],
            'notifications' => [
                'switch_success' => 'Tenant switched successfully',
                'switch_failed' => 'Failed to switch tenant',
                'test_success' => 'Connection test successful',
                'test_failed' => 'Connection test failed',
                'create_success' => 'Tenant created successfully',
            ],
        ],
    ],
];
