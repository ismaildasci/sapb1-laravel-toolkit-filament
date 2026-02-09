<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Listeners;

use Filament\Notifications\Notification;
use SapB1\Toolkit\Events\ApprovalCompleted;
use SapB1\Toolkit\Events\ApprovalRequested;

class ApprovalNotificationListener
{
    public function handleRequested(ApprovalRequested $event): void
    {
        if (! config('sapb1-filament.notifications.approval_events', false)) {
            return;
        }

        Notification::make()
            ->warning()
            ->title(__('sapb1-filament::notifications.approval.requested_title'))
            ->body(__('sapb1-filament::notifications.approval.requested_body', [
                'object_type' => $event->objectType,
                'object_entry' => $event->objectEntry,
            ]))
            ->sendToDatabase($this->getRecipients());
    }

    public function handleCompleted(ApprovalCompleted $event): void
    {
        if (! config('sapb1-filament.notifications.approval_events', false)) {
            return;
        }

        Notification::make()
            ->info()
            ->title(__('sapb1-filament::notifications.approval.completed_title'))
            ->body(__('sapb1-filament::notifications.approval.completed_body', [
                'object_type' => $event->objectType,
                'object_entry' => $event->objectEntry,
                'status' => $event->status->value,
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
