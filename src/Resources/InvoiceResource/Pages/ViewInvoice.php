<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use SapB1\Toolkit\Enums\DocumentStatus;
use SapB1\Toolkit\Filament\Resources\InvoiceResource;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->isOpen()),
            Actions\Action::make('cancel')
                ->label(__('sapb1-filament::resources.invoice.actions.cancel'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                /** @phpstan-ignore-next-line */
                ->action(fn () => $this->record->cancel())
                ->visible(fn () => $this->isOpen()),
        ];
    }

    private function isOpen(): bool
    {
        /** @phpstan-ignore-next-line */
        return $this->record->DocumentStatus === DocumentStatus::Open || $this->record->DocumentStatus === 'bost_Open';
    }
}
