<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\ItemResource\Widgets;

use Exception;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use SapB1\Toolkit\Models\Inventory\Item;

class ItemStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $stats = Cache::remember('sapb1_item_stats', 300, function (): array {
            try {
                $total = Item::query()->count();
                $active = Item::query()->where('Valid', 'tYES')->count();
                $zeroStock = Item::query()->where('QuantityOnStock', '<=', 0)->count();

                return [
                    'total' => $total,
                    'active' => $active,
                    'zero_stock' => $zeroStock,
                ];
            } catch (Exception) {
                return ['total' => 0, 'active' => 0, 'zero_stock' => 0];
            }
        });

        return [
            Stat::make(
                __('sapb1-filament::resources.item.widgets.total_items'),
                (string) $stats['total'],
            )->icon('heroicon-o-cube'),

            Stat::make(
                __('sapb1-filament::resources.item.widgets.active_items'),
                (string) $stats['active'],
            )
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(
                __('sapb1-filament::resources.item.widgets.zero_stock'),
                (string) $stats['zero_stock'],
            )
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stats['zero_stock'] > 0 ? 'danger' : 'success'),
        ];
    }
}
