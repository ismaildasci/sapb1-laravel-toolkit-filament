<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\OrderResource;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $today = now()->format('Y-m-d');
        $firstOfMonth = now()->startOfMonth()->format('Y-m-d');

        return [
            'all' => Tab::make(__('sapb1-filament::resources.order.tabs.all')),

            'open' => Tab::make(__('sapb1-filament::resources.order.tabs.open'))
                ->modifyQueryUsing(fn ($query) => $query->where('DocumentStatus', 'bost_Open')),

            'closed' => Tab::make(__('sapb1-filament::resources.order.tabs.closed'))
                ->modifyQueryUsing(fn ($query) => $query->where('DocumentStatus', 'bost_Close')),

            'this_month' => Tab::make(__('sapb1-filament::resources.order.tabs.this_month'))
                ->modifyQueryUsing(fn ($query) => $query->where('DocDate', '>=', $firstOfMonth)),

            'overdue' => Tab::make(__('sapb1-filament::resources.order.tabs.overdue'))
                ->modifyQueryUsing(fn ($query) => $query
                    ->where('DocDueDate', '<', $today)
                    ->where('DocumentStatus', 'bost_Open')),
        ];
    }
}
