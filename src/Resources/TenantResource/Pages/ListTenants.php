<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\TenantResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SapB1\Toolkit\Filament\Resources\TenantResource;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('sapb1-filament::resources.tenant.actions.create')),
        ];
    }

    /**
     * Override to use config-based data instead of Eloquent.
     */
    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        // Return null since we're using config-based data
        return null;
    }

    /**
     * Get records from configuration.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getTableRecords(): array
    {
        return TenantResource::getTenantData();
    }
}
