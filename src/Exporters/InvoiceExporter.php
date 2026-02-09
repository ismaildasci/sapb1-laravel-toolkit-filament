<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Exporters;

use SapB1\Toolkit\Models\Sales\Invoice;

class InvoiceExporter extends SapB1Exporter
{
    public function getHeaders(): array
    {
        return [
            'DocNum',
            'CardCode',
            'CardName',
            'DocDate',
            'DocTotal',
            'PaidToDate',
            'Balance',
            'DocumentStatus',
        ];
    }

    public function getRows(): iterable
    {
        $invoices = Invoice::query()
            ->select(['DocNum', 'CardCode', 'CardName', 'DocDate', 'DocTotal', 'PaidToDate', 'DocumentStatus'])
            ->orderBy('DocNum', 'desc')
            ->get();

        foreach ($invoices as $invoice) {
            $docTotal = (float) ($invoice->DocTotal ?? 0);
            $paidToDate = (float) ($invoice->PaidToDate ?? 0);

            yield [
                $invoice->DocNum,
                $invoice->CardCode,
                $invoice->CardName ?? '',
                $invoice->DocDate ?? '',
                $docTotal,
                $paidToDate,
                $docTotal - $paidToDate,
                is_object($invoice->DocumentStatus) ? $invoice->DocumentStatus->value : ($invoice->DocumentStatus ?? ''),
            ];
        }
    }

    public function getFilename(): string
    {
        return 'invoices-'.now()->format('Y-m-d-His').'.csv';
    }
}
