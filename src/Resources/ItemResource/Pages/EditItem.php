<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\ItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SapB1\Toolkit\Filament\Resources\ItemResource;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

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
