<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Exporters;

use SapB1\Toolkit\Models\Inventory\Item;

class ItemExporter extends SapB1Exporter
{
    public function getHeaders(): array
    {
        return [
            'ItemCode',
            'ItemName',
            'BarCode',
            'ItemType',
            'QuantityOnStock',
            'Available',
            'Valid',
        ];
    }

    public function getRows(): iterable
    {
        $items = Item::query()
            ->select(['ItemCode', 'ItemName', 'BarCode', 'ItemType', 'QuantityOnStock', 'QuantityOrderedByCustomers', 'Valid'])
            ->orderBy('ItemCode', 'asc')
            ->get();

        foreach ($items as $item) {
            $onStock = (float) ($item->QuantityOnStock ?? 0);
            $ordered = (float) ($item->QuantityOrderedByCustomers ?? 0);

            yield [
                $item->ItemCode,
                $item->ItemName ?? '',
                $item->BarCode ?? '',
                is_object($item->ItemType) ? $item->ItemType->value : ($item->ItemType ?? ''),
                $onStock,
                max(0, $onStock - $ordered),
                $item->Valid ?? '',
            ];
        }
    }

    public function getFilename(): string
    {
        return 'items-'.now()->format('Y-m-d-His').'.csv';
    }
}
