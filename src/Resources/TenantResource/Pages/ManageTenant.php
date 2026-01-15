<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\TenantResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use SapB1\Toolkit\Filament\Resources\TenantResource;
use SapB1\Toolkit\MultiTenant\MultiTenantService;

class ManageTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Handle the form submission to register a new tenant.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $multiTenant = app(MultiTenantService::class);

        // Register the tenant
        $multiTenant->registerTenant($data['id'], [
            'name' => $data['name'],
            'sap_url' => $data['sap_url'],
            'sap_database' => $data['sap_database'],
            'sap_username' => $data['sap_username'],
            'sap_password' => $data['sap_password'],
        ]);

        Notification::make()
            ->title(__('sapb1-filament::resources.tenant.notifications.create_success'))
            ->success()
            ->send();

        // Return a mock model since we're not using Eloquent
        // This is a workaround for config-based resources
        return new class extends \Illuminate\Database\Eloquent\Model
        {
            protected $guarded = [];
        };
    }
}
