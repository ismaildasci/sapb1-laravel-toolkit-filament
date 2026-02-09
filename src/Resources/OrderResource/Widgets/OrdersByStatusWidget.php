<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\OrderResource\Widgets;

use Exception;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use SapB1\Toolkit\Models\Sales\Order;

class OrdersByStatusWidget extends ChartWidget
{
    protected ?string $heading = null;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('sapb1-filament::resources.order.widgets.by_status');
    }

    protected function getData(): array
    {
        $data = Cache::remember('sapb1_order_status_chart', 300, function (): array {
            try {
                $open = Order::query()->where('DocumentStatus', 'bost_Open')->count();
                $closed = Order::query()->where('DocumentStatus', 'bost_Close')->count();
                $cancelled = Order::query()->where('DocumentStatus', 'bost_Cancelled')->count();

                return ['open' => $open, 'closed' => $closed, 'cancelled' => $cancelled];
            } catch (Exception) {
                return ['open' => 0, 'closed' => 0, 'cancelled' => 0];
            }
        });

        return [
            'datasets' => [
                [
                    'data' => [$data['open'], $data['closed'], $data['cancelled']],
                    'backgroundColor' => ['#10b981', '#6b7280', '#ef4444'],
                ],
            ],
            'labels' => [
                __('sapb1-filament::resources.order.widgets.status_open'),
                __('sapb1-filament::resources.order.widgets.status_closed'),
                __('sapb1-filament::resources.order.widgets.status_cancelled'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
