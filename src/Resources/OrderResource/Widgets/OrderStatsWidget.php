<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\OrderResource\Widgets;

use Exception;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use SapB1\Toolkit\Models\Sales\Order;

class OrderStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $stats = Cache::remember('sapb1_order_stats', 300, function (): array {
            try {
                $firstOfMonth = now()->startOfMonth()->format('Y-m-d');

                $totalThisMonth = Order::query()
                    ->where('DocDate', '>=', $firstOfMonth)
                    ->count();

                $openCount = Order::query()
                    ->where('DocumentStatus', 'bost_Open')
                    ->count();

                $openOrders = Order::query()
                    ->where('DocumentStatus', 'bost_Open')
                    ->select(['DocTotal'])
                    ->get();

                $openValue = (float) $openOrders->sum('DocTotal');

                $allThisMonth = Order::query()
                    ->where('DocDate', '>=', $firstOfMonth)
                    ->select(['DocTotal'])
                    ->get();

                $avgValue = $allThisMonth->count() > 0
                    ? (float) $allThisMonth->sum('DocTotal') / $allThisMonth->count()
                    : 0;

                return [
                    'total' => $totalThisMonth,
                    'open' => $openCount,
                    'open_value' => $openValue,
                    'avg_value' => $avgValue,
                ];
            } catch (Exception) {
                return ['total' => 0, 'open' => 0, 'open_value' => 0, 'avg_value' => 0];
            }
        });

        return [
            Stat::make(
                __('sapb1-filament::resources.order.widgets.total_this_month'),
                (string) $stats['total'],
            )->icon('heroicon-o-shopping-cart'),

            Stat::make(
                __('sapb1-filament::resources.order.widgets.open_orders'),
                (string) $stats['open'],
            )
                ->icon('heroicon-o-clock')
                ->color($stats['open'] > 0 ? 'warning' : 'success'),

            Stat::make(
                __('sapb1-filament::resources.order.widgets.open_value'),
                number_format((float) $stats['open_value'], 2).' TRY',
            )->icon('heroicon-o-currency-dollar'),

            Stat::make(
                __('sapb1-filament::resources.order.widgets.avg_value'),
                number_format((float) $stats['avg_value'], 2).' TRY',
            )->icon('heroicon-o-chart-bar'),
        ];
    }
}
