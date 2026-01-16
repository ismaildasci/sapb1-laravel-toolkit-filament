<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Notifications\Notification;
use SapB1\Toolkit\Filament\Models\SyncHistory;
use SapB1\Toolkit\Sync\LocalSyncService;

class TriggerSyncAction
{
    public function __construct(
        protected LocalSyncService $syncService
    ) {}

    /**
     * Execute incremental sync for an entity.
     */
    public function sync(string $entity): bool
    {
        $history = SyncHistory::start($entity, SyncHistory::TYPE_INCREMENTAL);

        try {
            $result = $this->syncService->sync($entity);

            $history->complete(
                synced: $result->synced(),
                created: $result->created,
                updated: $result->updated
            );

            Notification::make()
                ->success()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_success'))
                ->send();

            return true;
        } catch (Exception $e) {
            $history->fail($e->getMessage());

            Notification::make()
                ->danger()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_failed'))
                ->body($e->getMessage())
                ->send();

            return false;
        }
    }

    /**
     * Execute full sync with delete detection for an entity.
     */
    public function fullSync(string $entity): bool
    {
        $history = SyncHistory::start($entity, SyncHistory::TYPE_FULL_WITH_DELETES);

        try {
            $result = $this->syncService->fullSyncWithDeletes($entity);

            $history->complete(
                synced: $result->synced(),
                created: $result->created,
                updated: $result->updated,
                deleted: $result->deleted
            );

            Notification::make()
                ->success()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.full_sync_success'))
                ->send();

            return true;
        } catch (Exception $e) {
            $history->fail($e->getMessage());

            Notification::make()
                ->danger()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_failed'))
                ->body($e->getMessage())
                ->send();

            return false;
        }
    }

    /**
     * Sync all registered entities.
     */
    public function syncAll(): bool
    {
        try {
            $this->syncService->syncAll();

            Notification::make()
                ->success()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_all_success'))
                ->send();

            return true;
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_failed'))
                ->body($e->getMessage())
                ->send();

            return false;
        }
    }
}
