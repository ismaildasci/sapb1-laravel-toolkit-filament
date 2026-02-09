<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\AuditLogResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Exporters\AuditLogExporter;
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
                ->action(fn () => (new AuditLogExporter)->export()),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $today = now()->startOfDay();

        return [
            'all' => Tab::make(__('sapb1-filament::resources.audit_log.tabs.all')),

            'today' => Tab::make(__('sapb1-filament::resources.audit_log.tabs.today'))
                ->modifyQueryUsing(fn ($query) => $query->whereDate('created_at', '>=', $today)),

            'created' => Tab::make(__('sapb1-filament::resources.audit_log.tabs.created'))
                ->modifyQueryUsing(fn ($query) => $query->where('event', 'created')),

            'updated' => Tab::make(__('sapb1-filament::resources.audit_log.tabs.updated'))
                ->modifyQueryUsing(fn ($query) => $query->where('event', 'updated')),

            'deleted' => Tab::make(__('sapb1-filament::resources.audit_log.tabs.deleted'))
                ->modifyQueryUsing(fn ($query) => $query->where('event', 'deleted')),
        ];
    }
}
