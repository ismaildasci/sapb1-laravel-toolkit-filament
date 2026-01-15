<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use SapB1\Toolkit\Filament\Support\HealthChecker;

class SystemHealthWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $healthChecker = app(HealthChecker::class);
        $health = $healthChecker->check();

        return [
            $this->getSapConnectionStat($health['sap_connection']),
            $this->getAuditSystemStat($health['audit_system']),
            $this->getSyncSystemStat($health['sync_system']),
            $this->getCacheSystemStat($health['cache_system']),
            $this->getMultiTenantStat($health['multi_tenant']),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function getSapConnectionStat(array $data): Stat
    {
        $status = $data['status'] ?? 'unknown';
        $latency = $data['latency'] ?? null;

        $stat = Stat::make(
            label: __('sapb1-filament::widgets.health.sap_connection'),
            value: ucfirst($status)
        );

        if ($latency !== null) {
            $stat->description(sprintf('%dms latency', $latency));
        }

        return match ($status) {
            'healthy' => $stat->color('success')->icon('heroicon-o-check-circle'),
            'unhealthy' => $stat->color('warning')->icon('heroicon-o-exclamation-triangle'),
            'error' => $stat->color('danger')->icon('heroicon-o-x-circle'),
            default => $stat->color('gray')->icon('heroicon-o-question-mark-circle'),
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function getAuditSystemStat(array $data): Stat
    {
        $status = $data['status'] ?? 'disabled';
        $driver = $data['driver'] ?? 'unknown';

        return Stat::make(
            label: __('sapb1-filament::widgets.health.audit_system'),
            value: ucfirst($status)
        )
            ->description(sprintf('Driver: %s', $driver))
            ->color($status === 'enabled' ? 'success' : 'gray')
            ->icon($status === 'enabled' ? 'heroicon-o-clipboard-document-list' : 'heroicon-o-pause-circle');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function getSyncSystemStat(array $data): Stat
    {
        $status = $data['status'] ?? 'disabled';
        $entities = $data['configured_entities'] ?? 0;

        return Stat::make(
            label: __('sapb1-filament::widgets.health.sync_system'),
            value: ucfirst($status)
        )
            ->description(sprintf('%d entities configured', $entities))
            ->color($status === 'enabled' ? 'success' : 'gray')
            ->icon($status === 'enabled' ? 'heroicon-o-arrow-path' : 'heroicon-o-pause-circle');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function getCacheSystemStat(array $data): Stat
    {
        $status = $data['status'] ?? 'disabled';
        $store = $data['store'] ?? 'default';

        return Stat::make(
            label: __('sapb1-filament::widgets.health.cache_system'),
            value: ucfirst($status)
        )
            ->description(sprintf('Store: %s', $store))
            ->color($status === 'enabled' ? 'success' : 'gray')
            ->icon($status === 'enabled' ? 'heroicon-o-server-stack' : 'heroicon-o-pause-circle');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function getMultiTenantStat(array $data): Stat
    {
        $status = $data['status'] ?? 'disabled';
        $count = $data['tenants_count'] ?? 0;

        return Stat::make(
            label: __('sapb1-filament::widgets.health.multi_tenant'),
            value: ucfirst($status)
        )
            ->description(sprintf('%d tenants', $count))
            ->color($status === 'enabled' ? 'info' : 'gray')
            ->icon($status === 'enabled' ? 'heroicon-o-building-office-2' : 'heroicon-o-pause-circle');
    }
}
