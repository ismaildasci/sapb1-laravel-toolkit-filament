<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Listeners;

use Filament\Notifications\Notification;
use SapB1\Toolkit\Events\DocumentCancelled;
use SapB1\Toolkit\Events\DocumentClosed;
use SapB1\Toolkit\Events\DocumentCreated;

class DocumentNotificationListener
{
    public function handleCreated(DocumentCreated $event): void
    {
        if (! config('sapb1-filament.notifications.document_events', false)) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('sapb1-filament::notifications.document.created_title'))
            ->body(__('sapb1-filament::notifications.document.created_body', [
                'entity' => $event->entity,
                'doc_entry' => $event->docEntry,
            ]))
            ->sendToDatabase($this->getRecipients());
    }

    public function handleClosed(DocumentClosed $event): void
    {
        if (! config('sapb1-filament.notifications.document_events', false)) {
            return;
        }

        Notification::make()
            ->info()
            ->title(__('sapb1-filament::notifications.document.closed_title'))
            ->body(__('sapb1-filament::notifications.document.closed_body', [
                'entity' => $event->entity,
                'doc_entry' => $event->docEntry,
            ]))
            ->sendToDatabase($this->getRecipients());
    }

    public function handleCancelled(DocumentCancelled $event): void
    {
        if (! config('sapb1-filament.notifications.document_events', false)) {
            return;
        }

        Notification::make()
            ->danger()
            ->title(__('sapb1-filament::notifications.document.cancelled_title'))
            ->body(__('sapb1-filament::notifications.document.cancelled_body', [
                'entity' => $event->entity,
                'doc_entry' => $event->docEntry,
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
        $role = config('sapb1-filament.notifications.recipient_role');

        $query = $userModel::query();

        if ($role) {
            $query->where('role', $role);
        }

        return $query->get();
    }
}
