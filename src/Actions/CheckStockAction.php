<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use SapB1\Toolkit\Services\InventoryService;

class CheckStockAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('check_stock')
            ->label(__('sapb1-filament::resources.item.actions.check_stock'))
            ->icon('heroicon-o-archive-box')
            ->color('info')
            ->modalHeading(fn ($record) => __('sapb1-filament::resources.item.actions.check_stock_title', [
                'item' => $record->ItemCode ?? '',
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('sapb1-filament::resources.common.actions.close'))
            ->modalContent(function ($record) {
                try {
                    /** @var InventoryService $service */
                    $service = app(InventoryService::class);
                    $stockLevel = $service->getStockLevel((string) $record->ItemCode);

                    return view('sapb1-filament::components.stock-check', [
                        'itemCode' => $record->ItemCode,
                        'itemName' => $record->ItemName,
                        'stockLevel' => $stockLevel,
                        'onStock' => $record->QuantityOnStock ?? 0,
                        'orderedByCustomers' => $record->QuantityOrderedByCustomers ?? 0,
                        'orderedFromVendors' => $record->QuantityOrderedFromVendors ?? 0,
                    ]);
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('sapb1-filament::resources.item.notifications.stock_check_failed'))
                        ->body($e->getMessage())
                        ->send();

                    return null;
                }
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'check_stock';
    }
}
