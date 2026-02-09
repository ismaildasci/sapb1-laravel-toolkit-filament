<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\PartnerResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\PartnerResource;
use SapB1\Toolkit\Filament\Resources\PartnerResource\Widgets\PartnerStatsWidget;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PartnerStatsWidget::class,
        ];
    }

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
            'all' => Tab::make(__('sapb1-filament::resources.partner.tabs.all')),

            'customers' => Tab::make(__('sapb1-filament::resources.partner.tabs.customers'))
                ->modifyQueryUsing(fn ($query) => $query->where('CardType', 'cCustomer')),

            'suppliers' => Tab::make(__('sapb1-filament::resources.partner.tabs.suppliers'))
                ->modifyQueryUsing(fn ($query) => $query->where('CardType', 'cSupplier')),

            'leads' => Tab::make(__('sapb1-filament::resources.partner.tabs.leads'))
                ->modifyQueryUsing(fn ($query) => $query->where('CardType', 'cLid')),

            'with_balance' => Tab::make(__('sapb1-filament::resources.partner.tabs.with_balance'))
                ->modifyQueryUsing(fn ($query) => $query->where('CurrentAccountBalance', '>', 0)),
        ];
    }
}
