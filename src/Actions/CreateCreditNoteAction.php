<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class CreateCreditNoteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('create_credit_note')
            ->label(__('sapb1-filament::resources.invoice.actions.create_credit_note'))
            ->icon('heroicon-o-receipt-refund')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('sapb1-filament::resources.invoice.actions.credit_note_confirm_title'))
            ->modalDescription(__('sapb1-filament::resources.invoice.actions.credit_note_confirm_description'))
            ->action(function ($record): void {
                try {
                    $creditNote = $record->toCreditNote();
                    $creditNote->save();

                    Notification::make()
                        ->success()
                        ->title(__('sapb1-filament::resources.invoice.notifications.credit_note_created'))
                        ->body(__('sapb1-filament::resources.invoice.notifications.credit_note_created_body', [
                            'doc_num' => $creditNote->DocNum ?? $creditNote->DocEntry,
                        ]))
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('sapb1-filament::resources.invoice.notifications.credit_note_failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'create_credit_note';
    }
}
