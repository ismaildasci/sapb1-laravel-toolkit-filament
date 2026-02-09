<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use SapB1\Toolkit\Services\DocumentFlowService;

class ViewDocumentFlowAction extends Action
{
    protected string $flowType = 'order';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('view_document_flow')
            ->label(__('sapb1-filament::resources.common.actions.view_document_flow'))
            ->icon('heroicon-o-arrows-right-left')
            ->color('gray')
            ->modalHeading(__('sapb1-filament::resources.common.actions.document_flow_title'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('sapb1-filament::resources.common.actions.close'))
            ->modalContent(function ($record) {
                try {
                    /** @var DocumentFlowService $service */
                    $service = app(DocumentFlowService::class);

                    $flow = $service->getOrderFlow((int) $record->DocEntry);

                    /** @phpstan-ignore-next-line */
                    return view('sapb1-filament::components.document-flow', [
                        'flow' => $flow,
                    ]);
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('sapb1-filament::resources.common.notifications.flow_failed'))
                        ->body($e->getMessage())
                        ->send();

                    return null;
                }
            });
    }

    public function flowType(string $type): static
    {
        $this->flowType = $type;

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return 'view_document_flow';
    }
}
