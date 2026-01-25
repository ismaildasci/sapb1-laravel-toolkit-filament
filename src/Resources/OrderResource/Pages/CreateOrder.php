<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use SapB1\Toolkit\Filament\Resources\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
