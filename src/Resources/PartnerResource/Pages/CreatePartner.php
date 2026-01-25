<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\PartnerResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use SapB1\Toolkit\Filament\Resources\PartnerResource;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
