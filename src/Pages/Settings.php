<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Pages;

use Filament\Pages\Page;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;

class Settings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 99;

    protected string $view = 'sapb1-filament::pages.settings';

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::pages.settings.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return __('sapb1-filament::pages.settings.title');
    }

    public function getHeading(): string
    {
        return __('sapb1-filament::pages.settings.heading');
    }

    /**
     * Get the toolkit configuration.
     *
     * @return array<string, mixed>
     */
    public function getToolkitConfig(): array
    {
        return config('laravel-toolkit', []);
    }

    /**
     * Get the filament plugin configuration.
     *
     * @return array<string, mixed>
     */
    public function getFilamentConfig(): array
    {
        return config('sapb1-filament', []);
    }

    /**
     * Check if a feature is enabled in toolkit.
     */
    public function isFeatureEnabled(string $feature): bool
    {
        return (bool) config("laravel-toolkit.{$feature}.enabled", false);
    }

    /**
     * Get cache configuration.
     *
     * @return array<string, mixed>
     */
    public function getCacheConfig(): array
    {
        return [
            'enabled' => config('laravel-toolkit.cache.enabled', false),
            'ttl' => config('laravel-toolkit.cache.ttl', 3600),
            'store' => config('laravel-toolkit.cache.store', 'default'),
            'entities' => config('laravel-toolkit.cache.entities', []),
        ];
    }

    /**
     * Get sync configuration.
     *
     * @return array<string, mixed>
     */
    public function getSyncConfig(): array
    {
        return [
            'enabled' => config('laravel-toolkit.sync.enabled', true),
            'batch_size' => config('laravel-toolkit.sync.batch_size', 5000),
            'track_deletes' => config('laravel-toolkit.sync.track_deletes', true),
            'dispatch_events' => config('laravel-toolkit.sync.dispatch_events', true),
            'queue' => config('laravel-toolkit.sync.queue', []),
            'entities' => config('laravel-toolkit.sync.entities', []),
        ];
    }

    /**
     * Get audit configuration.
     *
     * @return array<string, mixed>
     */
    public function getAuditConfig(): array
    {
        return [
            'enabled' => config('laravel-toolkit.audit.enabled', true),
            'driver' => config('laravel-toolkit.audit.driver', 'database'),
            'retention_enabled' => config('laravel-toolkit.audit.retention.enabled', true),
            'retention_days' => config('laravel-toolkit.audit.retention.days', 365),
            'context' => config('laravel-toolkit.audit.context', []),
            'entities' => config('laravel-toolkit.audit.entities', []),
        ];
    }

    /**
     * Get multi-tenant configuration.
     *
     * @return array<string, mixed>
     */
    public function getMultiTenantConfig(): array
    {
        return [
            'enabled' => config('laravel-toolkit.multi_tenant.enabled', false),
            'resolver' => config('laravel-toolkit.multi_tenant.resolver', 'config'),
            'header' => config('laravel-toolkit.multi_tenant.header', 'X-Tenant-ID'),
            'tenants_count' => count(config('laravel-toolkit.multi_tenant.tenants', [])),
        ];
    }
}
