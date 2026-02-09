<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\InvoiceResource\Widgets;

use Exception;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use SapB1\Toolkit\Services\ReportingService;

class AgingChartWidget extends ChartWidget
{
    protected ?string $heading = null;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('sapb1-filament::resources.invoice.widgets.aging_chart');
    }

    protected function getData(): array
    {
        $data = Cache::remember('sapb1_invoice_aging', 300, function (): array {
            try {
                /** @var ReportingService $service */
                $service = app(ReportingService::class);
                $aging = $service->getAgingReport();

                return $aging['summary'] ?? [
                    'current' => 0,
                    '1-30' => 0,
                    '31-60' => 0,
                    '61-90' => 0,
                    'over90' => 0,
                ];
            } catch (Exception) {
                return [
                    'current' => 0,
                    '1-30' => 0,
                    '31-60' => 0,
                    '61-90' => 0,
                    'over90' => 0,
                ];
            }
        });

        return [
            'datasets' => [
                [
                    'label' => __('sapb1-filament::resources.invoice.widgets.outstanding'),
                    'data' => [
                        $data['current'] ?? 0,
                        $data['1-30'] ?? 0,
                        $data['31-60'] ?? 0,
                        $data['61-90'] ?? 0,
                        $data['over90'] ?? 0,
                    ],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#f97316', '#ef4444', '#7f1d1d'],
                ],
            ],
            'labels' => [
                __('sapb1-filament::resources.invoice.widgets.aging_current'),
                '1-30',
                '31-60',
                '61-90',
                '90+',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
