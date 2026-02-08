<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use SapB1\Toolkit\Services\PaymentService;

class RecordPaymentAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('record_payment')
            ->label(__('sapb1-filament::resources.invoice.actions.record_payment'))
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->form([
                TextInput::make('amount')
                    ->label(__('sapb1-filament::resources.invoice.actions.payment_amount'))
                    ->numeric()
                    ->required()
                    ->prefix('TRY')
                    ->minValue(0.01)
                    ->default(fn ($record) => ($record->DocTotal ?? 0) - ($record->PaidToDate ?? 0)),

                Select::make('payment_method')
                    ->label(__('sapb1-filament::resources.invoice.actions.payment_method'))
                    ->options([
                        'transfer' => __('sapb1-filament::resources.invoice.actions.payment_methods.transfer'),
                        'cash' => __('sapb1-filament::resources.invoice.actions.payment_methods.cash'),
                        'check' => __('sapb1-filament::resources.invoice.actions.payment_methods.check'),
                    ])
                    ->default('transfer')
                    ->required(),

                TextInput::make('account_code')
                    ->label(__('sapb1-filament::resources.invoice.actions.account_code'))
                    ->maxLength(15),
            ])
            ->action(function ($record, array $data): void {
                try {
                    /** @var PaymentService $service */
                    $service = app(PaymentService::class);
                    $service->payInvoice(
                        invoiceDocEntry: (int) $record->DocEntry,
                        amount: (float) $data['amount'],
                        paymentMethod: $data['payment_method'],
                        accountCode: $data['account_code'] ?? null,
                    );

                    Notification::make()
                        ->success()
                        ->title(__('sapb1-filament::resources.invoice.notifications.payment_recorded'))
                        ->body(__('sapb1-filament::resources.invoice.notifications.payment_recorded_body', [
                            'amount' => number_format((float) $data['amount'], 2),
                        ]))
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('sapb1-filament::resources.invoice.notifications.payment_failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            })
            ->visible(fn ($record) => (($record->DocTotal ?? 0) - ($record->PaidToDate ?? 0)) > 0);
    }

    public static function getDefaultName(): ?string
    {
        return 'record_payment';
    }
}
