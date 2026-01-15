# SAP B1 Laravel Toolkit - Filament Admin Panel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ismaildasci/laravel-sapb1-toolkit-filament.svg?style=flat-square)](https://packagist.org/packages/ismaildasci/laravel-sapb1-toolkit-filament)
[![Tests](https://img.shields.io/github/actions/workflow/status/ismaildasci/laravel-sapb1-toolkit-filament/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ismaildasci/laravel-sapb1-toolkit-filament/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/ismaildasci/laravel-sapb1-toolkit-filament.svg?style=flat-square)](https://packagist.org/packages/ismaildasci/laravel-sapb1-toolkit-filament)
[![License](https://img.shields.io/packagist/l/ismaildasci/laravel-sapb1-toolkit-filament.svg?style=flat-square)](https://packagist.org/packages/ismaildasci/laravel-sapb1-toolkit-filament)

A Filament 4.x admin panel plugin for [SAP Business One Laravel Toolkit](https://github.com/ismaildasci/laravel-sapb1-toolkit). Provides a beautiful UI for monitoring and managing your SAP Business One integration.

## Features

- **Dashboard** - System overview with health status widgets
- **Audit Logs** - Browse, filter, and export audit trail entries
- **Sync Management** - Monitor and trigger entity synchronization
- **Cache Management** - View cache status and flush cached data
- **Change Tracking** - Monitor watched entities and poll for changes
- **Multi-Tenant Support** - Manage and switch between tenants
- **Settings** - View all configuration in one place

## Requirements

- PHP 8.4+
- Laravel 11.x or 12.x
- Filament 4.5+
- [Laravel SAP B1 Toolkit](https://github.com/ismaildasci/laravel-sapb1-toolkit) 3.0+

## Installation

Install the package via composer:

```bash
composer require ismaildasci/laravel-sapb1-toolkit-filament
```

## Setup

Register the plugin in your Filament panel provider:

```php
// app/Providers/Filament/AdminPanelProvider.php

use SapB1\Toolkit\Filament\SapB1FilamentPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->path('admin')
        ->plugins([
            SapB1FilamentPlugin::make(),
        ]);
}
```

## Configuration

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="sapb1-filament-config"
```

### Publish Language Files (Optional)

```bash
php artisan vendor:publish --tag="sapb1-filament-lang"
```

### Plugin Options

```php
SapB1FilamentPlugin::make()
    // Navigation
    ->navigationGroup('SAP B1')
    ->navigationIcon('heroicon-o-building-office')
    ->navigationSort(100)

    // Enable/Disable features
    ->enableAuditFeature()
    ->enableSyncFeature()
    ->enableCacheFeature()
    ->enableMultiTenantFeature()
    ->enableChangeTrackingFeature()
    ->enableSettingsFeature();
```

### Disable Features

```php
SapB1FilamentPlugin::make()
    ->disableAuditFeature()           // Hide audit logs
    ->disableSyncFeature()            // Hide sync management
    ->disableCacheFeature()           // Hide cache management
    ->disableMultiTenantFeature()     // Hide tenant management
    ->disableChangeTrackingFeature()  // Hide change tracking
    ->disableSettingsFeature();       // Hide settings page
```

## Dashboard Widgets

| Widget | Description | Auto-Refresh |
|--------|-------------|--------------|
| **System Health** | SAP connection, audit, sync, cache, multi-tenant status | 30s |
| **Audit Activity** | Recent 10 audit log entries | - |
| **Sync Overview** | Entity sync status with manual sync actions | 10s |
| **Cache Stats** | Cache configuration and flush action | - |
| **Change Tracking** | Watched entities and polling status | 30s |

## Resources

### Audit Logs

Browse and filter audit trail:
- Filter by entity type, event type, date range
- View detailed changes (old/new values)
- Search by entity ID
- Bulk delete capability

### Sync Status

Monitor entity synchronization:
- View sync status per entity (idle/running/completed/failed)
- Trigger manual sync
- Full sync with delete detection
- Reset sync metadata
- Bulk sync selected entities

### Tenants (Multi-Tenant)

Manage SAP B1 tenants:
- View registered tenants from configuration
- Switch active tenant context
- Test tenant connection
- Visual indicator for current tenant

## Pages

### Dashboard

Central hub with all widgets showing system overview.

### Settings

Read-only view of all SAP B1 Toolkit configuration:
- Connection settings
- Audit configuration
- Sync settings
- Cache configuration
- Multi-tenant settings

## Customization

### Custom Navigation Group

```php
SapB1FilamentPlugin::make()
    ->navigationGroup('My Custom Group');
```

### Custom Navigation Icon

```php
SapB1FilamentPlugin::make()
    ->navigationIcon('heroicon-o-cog');
```

### Custom Navigation Sort Order

```php
SapB1FilamentPlugin::make()
    ->navigationSort(50);
```

## Testing

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage
```

## Static Analysis

```bash
composer analyse
```

## Code Formatting

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ismail Dasci](https://github.com/ismaildasci)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
