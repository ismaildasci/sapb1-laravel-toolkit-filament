<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\SyncHistoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Exporters\SyncHistoryExporter;
use SapB1\Toolkit\Filament\Resources\SyncHistoryResource;

class ListSyncHistory extends ListRecords
{
    protected static string $resource = SyncHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label(__('sapb1-filament::resources.common.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => (new SyncHistoryExporter)->export()),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $today = now()->startOfDay();

        return [
            'all' => Tab::make(__('sapb1-filament::resources.sync_history.tabs.all')),

            'running' => Tab::make(__('sapb1-filament::resources.sync_history.tabs.running'))
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'running')),

            'failed' => Tab::make(__('sapb1-filament::resources.sync_history.tabs.failed'))
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'failed')),

            'today' => Tab::make(__('sapb1-filament::resources.sync_history.tabs.today'))
                ->modifyQueryUsing(fn ($query) => $query->whereDate('started_at', '>=', $today)),
        ];
    }
}
