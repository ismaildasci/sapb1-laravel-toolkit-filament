# SAP B1 Laravel Toolkit - Filament Admin Panel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ismaildasci/laravel-sapb1-toolkit-filament.svg?style=flat-square)](https://packagist.org/packages/ismaildasci/laravel-sapb1-toolkit-filament)
[![Total Downloads](https://img.shields.io/packagist/dt/ismaildasci/laravel-sapb1-toolkit-filament.svg?style=flat-square)](https://packagist.org/packages/ismaildasci/laravel-sapb1-toolkit-filament)

Filament Admin Panel for [SAP Business One Laravel Toolkit](https://github.com/ismaildasci/laravel-sapb1-toolkit).

## Requirements

- PHP 8.4+
- Laravel 11.28+
- Filament 4.5+
- SAP B1 Laravel Toolkit 3.0+

## Installation

```bash
composer require ismaildasci/laravel-sapb1-toolkit-filament
```

## Setup

Register the plugin in your Filament panel provider:

```php
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

Publish the configuration file:

```bash
php artisan vendor:publish --tag="sapb1-filament-config"
```

### Plugin Options

```php
SapB1FilamentPlugin::make()
    ->auditEnabled(true)           // Enable audit log resource
    ->syncEnabled(true)            // Enable sync status resource
    ->cacheEnabled(true)           // Enable cache management
    ->multiTenantEnabled(true)     // Enable tenant management
    ->changeTrackingEnabled(true)  // Enable change tracking widget
    ->settingsEnabled(true)        // Enable settings page
    ->navigationGroup('SAP B1')    // Navigation group name
    ->navigationIcon('heroicon-o-building-office')
    ->navigationSort(100);
```

## Features

### Dashboard
- System health overview
- SAP B1 connection status
- Quick access to all features

### Audit Logs
- View all CRUD operations
- Filter by entity, event, user, date
- View before/after changes
- Export capabilities

### Sync Status
- Monitor sync status for all entities
- Trigger manual syncs
- View sync history and errors
- Full sync with delete detection

### Cache Management
- View cache configuration
- Flush all or entity-specific cache
- Monitor cache status

### Multi-Tenant Management
- View registered tenants
- Switch tenant context
- Test tenant connections
- Register new tenants

### Settings
- View all configuration
- Environment variables reference
- Read-only display

## Customization

### Custom Navigation

```php
SapB1FilamentPlugin::make()
    ->navigationGroup('My Custom Group')
    ->navigationIcon('heroicon-o-cog');
```

### Disable Features

```php
SapB1FilamentPlugin::make()
    ->multiTenantEnabled(false)  // Hide tenant management
    ->settingsEnabled(false);    // Hide settings page
```

## Testing

```bash
composer test
```

## PHPStan

```bash
composer analyse
```

## Code Formatting

```bash
composer format
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
