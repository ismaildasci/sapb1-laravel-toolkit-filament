# Changelog

All notable changes to `laravel-sapb1-toolkit-filament` will be documented in this file.

## [Unreleased]

## [1.1.0] - 2025-01-15

### Added
- CSV Export for Audit Logs (streaming download with configurable limit)
- Sync History Resource with full tracking
  - `sap_sync_history` migration for tracking all sync operations
  - `SyncHistory` model with start/complete/fail lifecycle
  - View sync history with filters (entity, status, sync type)
  - Duration tracking and human-readable formatting
- Standalone Action classes for reusability
  - `TriggerSyncAction` - Sync operations with history tracking
  - `FlushCacheAction` - Cache flush operations
  - `TestConnectionAction` - SAP B1 connection testing

### Changed
- Sync operations now automatically track history

## [1.0.0] - 2025-01-15

### Added
- Initial release
- Dashboard page with system overview widgets
- System Health Widget (SAP connection, audit, sync, cache, multi-tenant status)
- Audit Activity Widget (recent audit log entries)
- Sync Overview Widget (entity sync status with manual sync actions)
- Cache Stats Widget (cache configuration and flush action)
- Change Tracking Widget (watched entities and polling status)
- Audit Log Resource (browse, filter, view audit trail)
- Sync Metadata Resource (monitor and trigger entity synchronization)
- Tenant Resource (manage and switch between tenants)
- Settings Page (read-only configuration view)
- Filament 4.x compatibility
- Full test coverage (49 tests)
- English translations

### Dependencies
- PHP 8.4+
- Laravel 11.x / 12.x
- Filament 4.5+
- Laravel SAP B1 Toolkit 3.0+
