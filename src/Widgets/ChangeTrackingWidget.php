<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Widgets;

use Exception;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use SapB1\Toolkit\ChangeTracking\ChangeTrackingService;

class ChangeTrackingWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        try {
            $service = app(ChangeTrackingService::class);
            $watchers = $this->getWatcherInfo($service);

            return [
                Stat::make(
                    label: __('sapb1-filament::widgets.tracking.active_watchers'),
                    value: $watchers['count']
                )
                    ->description(__('sapb1-filament::widgets.tracking.entities_monitored'))
                    ->color($watchers['count'] > 0 ? 'success' : 'gray')
                    ->icon('heroicon-o-eye'),

                Stat::make(
                    label: __('sapb1-filament::widgets.tracking.entities'),
                    value: implode(', ', $watchers['entities']) ?: '-'
                )
                    ->description(__('sapb1-filament::widgets.tracking.being_watched'))
                    ->color('info')
                    ->icon('heroicon-o-queue-list'),

                Stat::make(
                    label: __('sapb1-filament::widgets.tracking.status'),
                    value: $watchers['count'] > 0
                        ? __('sapb1-filament::widgets.tracking.active')
                        : __('sapb1-filament::widgets.tracking.inactive')
                )
                    ->description(__('sapb1-filament::widgets.tracking.polling_status'))
                    ->color($watchers['count'] > 0 ? 'success' : 'warning')
                    ->icon($watchers['count'] > 0 ? 'heroicon-o-signal' : 'heroicon-o-signal-slash'),
            ];
        } catch (Exception $e) {
            return [
                Stat::make(
                    label: __('sapb1-filament::widgets.tracking.status'),
                    value: __('sapb1-filament::widgets.tracking.error')
                )
                    ->description($e->getMessage())
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('poll_all')
                ->label(__('sapb1-filament::widgets.tracking.poll_now'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function (): void {
                    $this->pollAll();
                }),
        ];
    }

    protected function pollAll(): void
    {
        try {
            $service = app(ChangeTrackingService::class);
            $changes = $service->pollAll();

            $totalChanges = collect($changes)->flatten()->count();

            Notification::make()
                ->title(__('sapb1-filament::widgets.tracking.poll_complete'))
                ->body(sprintf('%d changes detected', $totalChanges))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::widgets.tracking.poll_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getWatcherInfo(ChangeTrackingService $service): array
    {
        // Get registered watchers from the service
        // This depends on how ChangeTrackingService exposes its watchers
        $entities = [];
        $count = 0;

        // Try to get watcher information
        // The actual implementation depends on ChangeTrackingService API
        if (method_exists($service, 'getWatchers')) {
            $watchers = $service->getWatchers();
            $count = count($watchers);
            $entities = array_keys($watchers);
        }

        return [
            'count' => $count,
            'entities' => $entities,
        ];
    }
}
