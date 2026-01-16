<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Notifications\Notification;
use SapB1\Toolkit\Cache\CacheManager;

class FlushCacheAction
{
    /**
     * Flush all SAP B1 cache.
     */
    public function flushAll(): bool
    {
        try {
            CacheManager::flushAll();

            Notification::make()
                ->success()
                ->title(__('sapb1-filament::widgets.cache.flush_success'))
                ->send();

            return true;
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('sapb1-filament::widgets.cache.flush_failed'))
                ->body($e->getMessage())
                ->send();

            return false;
        }
    }

    /**
     * Flush cache for a specific entity.
     */
    public function flushEntity(string $entity): bool
    {
        try {
            CacheManager::invalidateEntity($entity);

            Notification::make()
                ->success()
                ->title(__('sapb1-filament::widgets.cache.flush_success'))
                ->body("Cache flushed for {$entity}")
                ->send();

            return true;
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('sapb1-filament::widgets.cache.flush_failed'))
                ->body($e->getMessage())
                ->send();

            return false;
        }
    }
}
