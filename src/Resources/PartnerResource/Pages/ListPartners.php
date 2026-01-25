<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\PartnerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\PartnerResource;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
