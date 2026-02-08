<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\ItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\ItemResource;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

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
        return [
            'all' => Tab::make(__('sapb1-filament::resources.item.tabs.all')),

            'active' => Tab::make(__('sapb1-filament::resources.item.tabs.active'))
                ->modifyQueryUsing(fn ($query) => $query->where('Valid', 'tYES')),

            'in_stock' => Tab::make(__('sapb1-filament::resources.item.tabs.in_stock'))
                ->modifyQueryUsing(fn ($query) => $query->where('QuantityOnStock', '>', 0)),

            'out_of_stock' => Tab::make(__('sapb1-filament::resources.item.tabs.out_of_stock'))
                ->modifyQueryUsing(fn ($query) => $query->where('QuantityOnStock', '<=', 0)),

            'sales_items' => Tab::make(__('sapb1-filament::resources.item.tabs.sales_items'))
                ->modifyQueryUsing(fn ($query) => $query->where('SalesItem', 'tYES')),
        ];
    }
}
