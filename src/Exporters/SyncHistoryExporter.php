<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Exporters;

use SapB1\Toolkit\Filament\Models\SyncHistory;

class SyncHistoryExporter extends SapB1Exporter
{
    public function getHeaders(): array
    {
        return [
            'Entity',
            'Sync Type',
            'Status',
            'Records Synced',
            'Duration',
            'Started At',
        ];
    }

    public function getRows(): iterable
    {
        $records = SyncHistory::query()
            ->latest('started_at')
            ->limit(10000)
            ->get();

        foreach ($records as $record) {
            yield [
                $record->entity ?? '',
                $record->sync_type ?? '',
                $record->status ?? '',
                $record->records_synced ?? 0,
                $record->duration ?? '',
                $record->started_at ?? '',
            ];
        }
    }

    public function getFilename(): string
    {
        return 'sync-history-'.now()->format('Y-m-d-His').'.csv';
    }
}
