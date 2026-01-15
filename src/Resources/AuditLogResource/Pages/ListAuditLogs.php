<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\AuditLogResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\AuditLogResource;

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

    protected function exportAuditLogs(): void
    {
        // Export functionality - can be implemented with Laravel Excel or similar
        $this->dispatch('notify', [
            'status' => 'info',
            'message' => 'Export functionality coming soon',
        ]);
    }
}
