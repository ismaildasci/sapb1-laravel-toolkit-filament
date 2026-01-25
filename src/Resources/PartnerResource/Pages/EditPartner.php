<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\PartnerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SapB1\Toolkit\Filament\Resources\PartnerResource;

class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
