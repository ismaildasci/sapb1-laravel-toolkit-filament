<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\SyncHistoryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\SyncHistoryResource;

class ListSyncHistory extends ListRecords
{
    protected static string $resource = SyncHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
