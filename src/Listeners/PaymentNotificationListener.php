<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Listeners;

use Filament\Notifications\Notification;
use SapB1\Toolkit\Events\PaymentReceived;

class PaymentNotificationListener
{
    public function handle(PaymentReceived $event): void
    {
        if (! config('sapb1-filament.notifications.payment_events', false)) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('sapb1-filament::notifications.payment.received_title'))
            ->body(__('sapb1-filament::notifications.payment.received_body', [
                'card_code' => $event->cardCode,
                'amount' => number_format($event->amount, 2),
            ]))
            ->sendToDatabase($this->getRecipients());
    }

    /**
     * @return \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    protected function getRecipients(): \Illuminate\Support\Collection
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model> $userModel */
        $userModel = config('sapb1-filament.notifications.recipient_model', 'App\\Models\\User');

        return $userModel::query()->get();
    }
}
