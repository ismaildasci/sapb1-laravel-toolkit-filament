<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\ItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use SapB1\Toolkit\Filament\Resources\ItemResource;

class ViewItem extends ViewRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
