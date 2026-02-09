<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Exporters;

use SapB1\Toolkit\Models\BusinessPartner\Partner;

class PartnerExporter extends SapB1Exporter
{
    public function getHeaders(): array
    {
        return [
            'CardCode',
            'CardName',
            'CardType',
            'Phone1',
            'EmailAddress',
            'City',
            'CurrentAccountBalance',
            'Valid',
        ];
    }

    public function getRows(): iterable
    {
        $partners = Partner::query()
            ->select(['CardCode', 'CardName', 'CardType', 'Phone1', 'EmailAddress', 'City', 'CurrentAccountBalance', 'Valid'])
            ->orderBy('CardCode', 'asc')
            ->get();

        foreach ($partners as $partner) {
            yield [
                $partner->CardCode,
                $partner->CardName ?? '',
                is_object($partner->CardType) ? $partner->CardType->value : ($partner->CardType ?? ''),
                $partner->Phone1 ?? '',
                $partner->EmailAddress ?? '',
                $partner->City ?? '',
                $partner->CurrentAccountBalance ?? 0,
                $partner->Valid ?? '',
            ];
        }
    }

    public function getFilename(): string
    {
        return 'partners-'.now()->format('Y-m-d-His').'.csv';
    }
}
