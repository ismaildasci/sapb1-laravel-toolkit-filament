<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\InvoiceResource;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

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
            'all' => Tab::make(__('sapb1-filament::resources.invoice.tabs.all')),

            'open' => Tab::make(__('sapb1-filament::resources.invoice.tabs.open'))
                ->modifyQueryUsing(fn ($query) => $query->where('DocumentStatus', 'bost_Open')),

            'unpaid' => Tab::make(__('sapb1-filament::resources.invoice.tabs.unpaid'))
                ->modifyQueryUsing(fn ($query) => $query->filter('PaidToDate lt DocTotal')),

            'overdue' => Tab::make(__('sapb1-filament::resources.invoice.tabs.overdue'))
                ->modifyQueryUsing(fn ($query) => $query
                    ->where('DocDueDate', '<', $today)
                    ->where('DocumentStatus', 'bost_Open')),

            'this_month' => Tab::make(__('sapb1-filament::resources.invoice.tabs.this_month'))
                ->modifyQueryUsing(fn ($query) => $query->where('DocDate', '>=', $firstOfMonth)),
        ];
    }
}
