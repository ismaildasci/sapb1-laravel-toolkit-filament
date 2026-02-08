<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use SapB1\Toolkit\Services\DocumentFlowService;

class CopyToInvoiceAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('copy_to_invoice')
            ->label(__('sapb1-filament::resources.order.actions.copy_to_invoice'))
            ->icon('heroicon-o-document-text')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('sapb1-filament::resources.order.actions.copy_to_invoice_confirm_title'))
            ->modalDescription(__('sapb1-filament::resources.order.actions.copy_to_invoice_confirm_description'))
            ->action(function ($record): void {
                try {
                    /** @var DocumentFlowService $service */
                    $service = app(DocumentFlowService::class);
                    $invoice = $service->orderToInvoice($record->DocEntry);

                    Notification::make()
                        ->success()
                        ->title(__('sapb1-filament::resources.order.notifications.invoice_created'))
                        ->body(__('sapb1-filament::resources.order.notifications.invoice_created_body', [
                            'doc_num' => $invoice->docNum ?? $invoice->docEntry,
                        ]))
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('sapb1-filament::resources.order.notifications.invoice_failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            })
            ->visible(fn ($record) => ($record->DocumentStatus ?? null) === 'bost_Open'
                || ($record->DocumentStatus?->value ?? null) === 'bost_Open');
    }

    public static function getDefaultName(): ?string
    {
        return 'copy_to_invoice';
    }
}
