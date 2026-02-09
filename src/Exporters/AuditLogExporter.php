<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Exporters;

use SapB1\Toolkit\Models\AuditLog;

class AuditLogExporter extends SapB1Exporter
{
    public function getHeaders(): array
    {
        return [
            'ID',
            'Entity Type',
            'Entity ID',
            'Event',
            'User ID',
            'IP Address',
            'Created At',
        ];
    }

    public function getRows(): iterable
    {
        $maxExport = (int) config('sapb1-filament.audit.max_export', 10000);

        $logs = AuditLog::query()
            ->latest()
            ->limit($maxExport)
            ->get();

        foreach ($logs as $log) {
            /** @var mixed $createdAt */
            $createdAt = $log->getAttribute('created_at');

            yield [
                $log->getAttribute('id'),
                $log->getAttribute('entity_type'),
                $log->getAttribute('entity_id'),
                $log->getAttribute('event'),
                $log->getAttribute('user_id') ?? '',
                $log->getAttribute('ip_address') ?? '',
                $createdAt instanceof \DateTimeInterface ? $createdAt->format('c') : '',
            ];
        }
    }

    public function getFilename(): string
    {
        return 'audit-logs-'.now()->format('Y-m-d-His').'.csv';
    }
}
