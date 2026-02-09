<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Exporters;

use SapB1\Toolkit\Models\Sales\Order;

class OrderExporter extends SapB1Exporter
{
    public function getHeaders(): array
    {
        return [
            'DocNum',
            'CardCode',
            'CardName',
            'DocDate',
            'DocDueDate',
            'DocTotal',
            'VatSum',
            'DocumentStatus',
        ];
    }

    public function getRows(): iterable
    {
        $orders = Order::query()
            ->select(['DocNum', 'CardCode', 'CardName', 'DocDate', 'DocDueDate', 'DocTotal', 'VatSum', 'DocumentStatus'])
            ->orderBy('DocNum', 'desc')
            ->get();

        foreach ($orders as $order) {
            yield [
                $order->DocNum,
                $order->CardCode,
                $order->CardName ?? '',
                $order->DocDate ?? '',
                $order->DocDueDate ?? '',
                $order->DocTotal ?? 0,
                $order->VatSum ?? 0,
                is_object($order->DocumentStatus) ? $order->DocumentStatus->value : ($order->DocumentStatus ?? ''),
            ];
        }
    }

    public function getFilename(): string
    {
        return 'orders-'.now()->format('Y-m-d-His').'.csv';
    }
}
