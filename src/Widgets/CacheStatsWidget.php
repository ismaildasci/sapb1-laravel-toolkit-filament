<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Widgets;

use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use SapB1\Toolkit\Cache\CacheManager;

class CacheStatsWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected function getStats(): array
    {
        $cacheEnabled = (bool) config('laravel-toolkit.cache.enabled', false);
        $cacheStore = config('laravel-toolkit.cache.store', 'default');
        $cacheTtl = config('laravel-toolkit.cache.ttl', 3600);
        $entities = config('laravel-toolkit.cache.entities', []);

        $enabledEntities = collect($entities)
            ->filter(fn ($config) => $config['enabled'] ?? false)
            ->count();

        return [
            Stat::make(
                label: __('sapb1-filament::widgets.cache.status'),
                value: $cacheEnabled
                    ? __('sapb1-filament::widgets.cache.enabled')
                    : __('sapb1-filament::widgets.cache.disabled')
            )
                ->color($cacheEnabled ? 'success' : 'gray')
                ->icon($cacheEnabled ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

            Stat::make(
                label: __('sapb1-filament::widgets.cache.store'),
                value: ucfirst($cacheStore)
            )
                ->color('info')
                ->icon('heroicon-o-server-stack'),

            Stat::make(
                label: __('sapb1-filament::widgets.cache.ttl'),
                value: $this->formatTtl($cacheTtl)
            )
                ->color('info')
                ->icon('heroicon-o-clock'),

            Stat::make(
                label: __('sapb1-filament::widgets.cache.entities'),
                value: sprintf('%d / %d', $enabledEntities, count($entities))
            )
                ->description(__('sapb1-filament::widgets.cache.entities_enabled'))
                ->color($enabledEntities > 0 ? 'success' : 'gray')
                ->icon('heroicon-o-table-cells'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('flush_cache')
                ->label(__('sapb1-filament::widgets.cache.flush_all'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('sapb1-filament::widgets.cache.flush_confirm_title'))
                ->modalDescription(__('sapb1-filament::widgets.cache.flush_confirm_description'))
                ->action(function (): void {
                    $this->flushAllCache();
                }),
        ];
    }

    protected function flushAllCache(): void
    {
        try {
            CacheManager::flushAll();

            Notification::make()
                ->title(__('sapb1-filament::widgets.cache.flush_success'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::widgets.cache.flush_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function formatTtl(int $seconds): string
    {
        if ($seconds >= 86400) {
            $days = floor($seconds / 86400);

            return sprintf('%d %s', $days, $days === 1 ? 'day' : 'days');
        }

        if ($seconds >= 3600) {
            $hours = floor($seconds / 3600);

            return sprintf('%d %s', $hours, $hours === 1 ? 'hour' : 'hours');
        }

        if ($seconds >= 60) {
            $minutes = floor($seconds / 60);

            return sprintf('%d %s', $minutes, $minutes === 1 ? 'minute' : 'minutes');
        }

        return sprintf('%d seconds', $seconds);
    }
}
