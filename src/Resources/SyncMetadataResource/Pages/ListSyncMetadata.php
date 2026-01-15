<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\SyncMetadataResource\Pages;

use Exception;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\SyncMetadataResource;
use SapB1\Toolkit\Sync\LocalSyncService;

class ListSyncMetadata extends ListRecords
{
    protected static string $resource = SyncMetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync_all')
                ->label(__('sapb1-filament::resources.sync_metadata.actions.sync_all'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->action(fn () => $this->syncAll()),
        ];
    }

    protected function syncAll(): void
    {
        try {
            $syncService = app(LocalSyncService::class);
            $results = $syncService->syncAll();

            $totalCreated = collect($results)->sum('created');
            $totalUpdated = collect($results)->sum('updated');

            Notification::make()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_all_success'))
                ->body(sprintf(
                    'Entities: %d, Created: %d, Updated: %d',
                    count($results),
                    $totalCreated,
                    $totalUpdated
                ))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
