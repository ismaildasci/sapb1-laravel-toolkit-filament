<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use SapB1\Toolkit\Filament\Pages\Dashboard;
use SapB1\Toolkit\Filament\Pages\Settings;
use SapB1\Toolkit\Filament\Resources\AuditLogResource;
use SapB1\Toolkit\Filament\Resources\InvoiceResource;
use SapB1\Toolkit\Filament\Resources\ItemResource;
use SapB1\Toolkit\Filament\Resources\OrderResource;
use SapB1\Toolkit\Filament\Resources\PartnerResource;
use SapB1\Toolkit\Filament\Resources\SyncHistoryResource;
use SapB1\Toolkit\Filament\Resources\SyncMetadataResource;
use SapB1\Toolkit\Filament\Resources\TenantResource;
use SapB1\Toolkit\Filament\Widgets\AuditActivityWidget;
use SapB1\Toolkit\Filament\Widgets\CacheStatsWidget;
use SapB1\Toolkit\Filament\Widgets\ChangeTrackingWidget;
use SapB1\Toolkit\Filament\Widgets\SyncOverviewWidget;
use SapB1\Toolkit\Filament\Widgets\SystemHealthWidget;

final class SapB1FilamentPlugin implements Plugin
{
    private bool $dashboardEnabled = true;

    private bool $auditEnabled = true;

    private bool $syncEnabled = true;

    private bool $cacheEnabled = true;

    private bool $multiTenantEnabled = true;

    private bool $changeTrackingEnabled = true;

    private bool $settingsEnabled = true;

    private bool $entitiesEnabled = true;

    private bool $partnerEnabled = true;

    private bool $itemEnabled = true;

    private bool $orderEnabled = true;

    private bool $invoiceEnabled = true;

    private string $navigationGroup = 'SAP B1';

    private ?string $navigationIcon = 'heroicon-o-building-office';

    private int $navigationSort = 100;

    public static function make(): static
    {
        return app(self::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'sapb1-toolkit';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages($this->getPages())
            ->resources($this->getResources())
            ->widgets($this->getWidgets());
    }

    public function boot(Panel $panel): void
    {
        // Register any additional services or event listeners
    }

    /**
     * @return array<class-string>
     */
    protected function getPages(): array
    {
        $pages = [];

        if ($this->dashboardEnabled) {
            $pages[] = Dashboard::class;
        }

        if ($this->settingsEnabled) {
            $pages[] = Settings::class;
        }

        return $pages;
    }

    /**
     * @return array<class-string>
     */
    protected function getResources(): array
    {
        $resources = [];

        if ($this->auditEnabled) {
            $resources[] = AuditLogResource::class;
        }

        if ($this->syncEnabled) {
            $resources[] = SyncMetadataResource::class;
            $resources[] = SyncHistoryResource::class;
        }

        if ($this->multiTenantEnabled) {
            $resources[] = TenantResource::class;
        }

        if ($this->entitiesEnabled && $this->partnerEnabled) {
            $resources[] = PartnerResource::class;
        }

        if ($this->entitiesEnabled && $this->itemEnabled) {
            $resources[] = ItemResource::class;
        }

        if ($this->entitiesEnabled && $this->orderEnabled) {
            $resources[] = OrderResource::class;
        }

        if ($this->entitiesEnabled && $this->invoiceEnabled) {
            $resources[] = InvoiceResource::class;
        }

        return $resources;
    }

    /**
     * @return array<class-string>
     */
    protected function getWidgets(): array
    {
        $widgets = [];

        $widgets[] = SystemHealthWidget::class;

        if ($this->auditEnabled) {
            $widgets[] = AuditActivityWidget::class;
        }

        if ($this->syncEnabled) {
            $widgets[] = SyncOverviewWidget::class;
        }

        if ($this->cacheEnabled) {
            $widgets[] = CacheStatsWidget::class;
        }

        if ($this->changeTrackingEnabled) {
            $widgets[] = ChangeTrackingWidget::class;
        }

        return $widgets;
    }

    // Fluent configuration methods

    public function dashboardEnabled(bool $enabled = true): static
    {
        $this->dashboardEnabled = $enabled;

        return $this;
    }

    public function auditEnabled(bool $enabled = true): static
    {
        $this->auditEnabled = $enabled;

        return $this;
    }

    public function syncEnabled(bool $enabled = true): static
    {
        $this->syncEnabled = $enabled;

        return $this;
    }

    public function cacheEnabled(bool $enabled = true): static
    {
        $this->cacheEnabled = $enabled;

        return $this;
    }

    public function multiTenantEnabled(bool $enabled = true): static
    {
        $this->multiTenantEnabled = $enabled;

        return $this;
    }

    public function changeTrackingEnabled(bool $enabled = true): static
    {
        $this->changeTrackingEnabled = $enabled;

        return $this;
    }

    public function settingsEnabled(bool $enabled = true): static
    {
        $this->settingsEnabled = $enabled;

        return $this;
    }

    public function entitiesEnabled(bool $enabled = true): static
    {
        $this->entitiesEnabled = $enabled;

        return $this;
    }

    public function partnerEnabled(bool $enabled = true): static
    {
        $this->partnerEnabled = $enabled;

        return $this;
    }

    public function itemEnabled(bool $enabled = true): static
    {
        $this->itemEnabled = $enabled;

        return $this;
    }

    public function orderEnabled(bool $enabled = true): static
    {
        $this->orderEnabled = $enabled;

        return $this;
    }

    public function invoiceEnabled(bool $enabled = true): static
    {
        $this->invoiceEnabled = $enabled;

        return $this;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    // Getters

    public function isDashboardEnabled(): bool
    {
        return $this->dashboardEnabled;
    }

    public function isAuditEnabled(): bool
    {
        return $this->auditEnabled;
    }

    public function isSyncEnabled(): bool
    {
        return $this->syncEnabled;
    }

    public function isCacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    public function isMultiTenantEnabled(): bool
    {
        return $this->multiTenantEnabled;
    }

    public function isChangeTrackingEnabled(): bool
    {
        return $this->changeTrackingEnabled;
    }

    public function isSettingsEnabled(): bool
    {
        return $this->settingsEnabled;
    }

    public function isEntitiesEnabled(): bool
    {
        return $this->entitiesEnabled;
    }

    public function isPartnerEnabled(): bool
    {
        return $this->entitiesEnabled && $this->partnerEnabled;
    }

    public function isItemEnabled(): bool
    {
        return $this->entitiesEnabled && $this->itemEnabled;
    }

    public function isOrderEnabled(): bool
    {
        return $this->entitiesEnabled && $this->orderEnabled;
    }

    public function isInvoiceEnabled(): bool
    {
        return $this->entitiesEnabled && $this->invoiceEnabled;
    }

    public function getNavigationGroup(): string
    {
        return $this->navigationGroup;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): int
    {
        return $this->navigationSort;
    }
}
