<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use SapB1\Toolkit\Services\AttachmentService;

class UploadAttachmentAction extends Action
{
    protected string $entityEndpoint = 'Orders';

    protected string $entityKeyField = 'DocEntry';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('upload_attachment')
            ->label(__('sapb1-filament::resources.common.actions.upload_attachment'))
            ->icon('heroicon-o-paper-clip')
            ->color('gray')
            ->form([
                FileUpload::make('attachment')
                    ->label(__('sapb1-filament::resources.common.actions.attachment_file'))
                    ->required()
                    ->maxSize(10240)
                    ->disk('local')
                    ->directory('sapb1-attachments')
                    ->visibility('private'),
            ])
            ->action(function ($record, array $data): void {
                try {
                    /** @var AttachmentService $service */
                    $service = app(AttachmentService::class);
                    $key = $record->{$this->entityKeyField};

                    $service->upload(
                        $this->entityEndpoint,
                        $key,
                        $data['attachment'],
                    );

                    Notification::make()
                        ->success()
                        ->title(__('sapb1-filament::resources.common.notifications.attachment_uploaded'))
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('sapb1-filament::resources.common.notifications.attachment_failed'))
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    public function entityEndpoint(string $endpoint): static
    {
        $this->entityEndpoint = $endpoint;

        return $this;
    }

    public function entityKeyField(string $field): static
    {
        $this->entityKeyField = $field;

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return 'upload_attachment';
    }
}
