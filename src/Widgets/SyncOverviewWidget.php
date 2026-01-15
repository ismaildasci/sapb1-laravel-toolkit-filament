<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Widgets;

use Exception;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use SapB1\Toolkit\Sync\LocalSyncService;
use SapB1\Toolkit\Sync\SyncMetadata;

class SyncOverviewWidget extends BaseWidget
{
    protected static ?string $heading = null;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected static ?string $pollingInterval = '10s';

    public function getHeading(): ?string
    {
        return __('sapb1-filament::widgets.sync.heading');
    }

    protected function getTableQuery(): Builder
    {
        return SyncMetadata::query()->orderBy('entity');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('entity')
                ->label(__('sapb1-filament::widgets.sync.entity'))
                ->searchable()
                ->sortable(),

            BadgeColumn::make('status')
                ->label(__('sapb1-filament::widgets.sync.status'))
                ->colors([
                    'success' => 'completed',
                    'warning' => 'running',
                    'danger' => 'failed',
                    'gray' => 'pending',
                ]),

            TextColumn::make('last_synced_at')
                ->label(__('sapb1-filament::widgets.sync.last_sync'))
                ->since()
                ->sortable()
                ->placeholder(__('sapb1-filament::widgets.sync.never')),

            TextColumn::make('synced_count')
                ->label(__('sapb1-filament::widgets.sync.records'))
                ->numeric()
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        if (! config('sapb1-filament.sync.allow_manual_sync', true)) {
            return [];
        }

        return [
            Tables\Actions\Action::make('sync')
                ->label(__('sapb1-filament::widgets.sync.sync_now'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function (SyncMetadata $record): void {
                    $this->triggerSync($record->entity);
                }),

            Tables\Actions\Action::make('full_sync')
                ->label(__('sapb1-filament::widgets.sync.full_sync'))
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(__('sapb1-filament::widgets.sync.full_sync_warning'))
                ->action(function (SyncMetadata $record): void {
                    $this->triggerFullSync($record->entity);
                }),
        ];
    }

    protected function triggerSync(string $entity): void
    {
        try {
            $syncService = app(LocalSyncService::class);
            $result = $syncService->sync($entity);

            Notification::make()
                ->title(__('sapb1-filament::widgets.sync.sync_success'))
                ->body(sprintf(
                    'Created: %d, Updated: %d, Duration: %.2fs',
                    $result->created,
                    $result->updated,
                    $result->duration
                ))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::widgets.sync.sync_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function triggerFullSync(string $entity): void
    {
        try {
            $syncService = app(LocalSyncService::class);
            $result = $syncService->fullSyncWithDeletes($entity);

            Notification::make()
                ->title(__('sapb1-filament::widgets.sync.full_sync_success'))
                ->body(sprintf(
                    'Created: %d, Updated: %d, Deleted: %d, Duration: %.2fs',
                    $result->created,
                    $result->updated,
                    $result->deleted,
                    $result->duration
                ))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::widgets.sync.sync_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
