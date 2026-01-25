<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SapB1\Toolkit\Enums\DocumentStatus;
use SapB1\Toolkit\Filament\Resources\InvoiceResource;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('cancel')
                ->label(__('sapb1-filament::resources.invoice.actions.cancel'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                /** @phpstan-ignore-next-line */
                ->action(fn () => $this->record->cancel())
                ->visible(fn () => $this->isOpen()),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->isOpen()),
        ];
    }

    private function isOpen(): bool
    {
        /** @phpstan-ignore-next-line */
        return $this->record->DocumentStatus === DocumentStatus::Open || $this->record->DocumentStatus === 'bost_Open';
    }
}
