<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\AuditLogResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;
use SapB1\Toolkit\Filament\Resources\AuditLogResource;
use SapB1\Toolkit\Models\AuditLog;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label(__('sapb1-filament::resources.audit_log.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportAuditLogs()),
        ];
    }

    public function exportAuditLogs(): StreamedResponse
    {
        $filename = 'audit-logs-'.now()->format('Y-m-d-His').'.csv';

        return Response::streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, [
                'ID',
                'Entity Type',
                'Entity ID',
                'Event',
                'User ID',
                'Tenant ID',
                'IP Address',
                'User Agent',
                'Old Values',
                'New Values',
                'Created At',
            ]);

            // Get max export limit from config
            $maxExport = config('sapb1-filament.audit.max_export', 10000);

            // Stream records
            AuditLog::query()
                ->latest()
                ->limit($maxExport)
                ->each(function (AuditLog $log) use ($handle) {
                    /** @var mixed $createdAt */
                    $createdAt = $log->getAttribute('created_at');

                    fputcsv($handle, [
                        $log->getAttribute('id'),
                        $log->getAttribute('entity_type'),
                        $log->getAttribute('entity_id'),
                        $log->getAttribute('event'),
                        $log->getAttribute('user_id') ?? '',
                        $log->getAttribute('tenant_id') ?? '',
                        $log->getAttribute('ip_address') ?? '',
                        $log->getAttribute('user_agent') ?? '',
                        json_encode($log->getAttribute('old_values') ?? []),
                        json_encode($log->getAttribute('new_values') ?? []),
                        $createdAt instanceof \DateTimeInterface ? $createdAt->format('c') : '',
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
