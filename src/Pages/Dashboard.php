<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Pages;

use Filament\Pages\Page;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Filament\Widgets\AuditActivityWidget;
use SapB1\Toolkit\Filament\Widgets\CacheStatsWidget;
use SapB1\Toolkit\Filament\Widgets\ChangeTrackingWidget;
use SapB1\Toolkit\Filament\Widgets\SyncOverviewWidget;
use SapB1\Toolkit\Filament\Widgets\SystemHealthWidget;

class Dashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?int $navigationSort = 1;

    protected string $view = 'sapb1-filament::pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::pages.dashboard.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return __('sapb1-filament::pages.dashboard.title');
    }

    public function getHeading(): string
    {
        return __('sapb1-filament::pages.dashboard.heading');
    }

    public function getSubheading(): ?string
    {
        return __('sapb1-filament::pages.dashboard.subheading');
    }

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            SystemHealthWidget::class,
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        $widgets = [];

        if (SapB1FilamentPlugin::get()->isAuditEnabled()) {
            $widgets[] = AuditActivityWidget::class;
        }

        if (SapB1FilamentPlugin::get()->isSyncEnabled()) {
            $widgets[] = SyncOverviewWidget::class;
        }

        if (SapB1FilamentPlugin::get()->isCacheEnabled()) {
            $widgets[] = CacheStatsWidget::class;
        }

        if (SapB1FilamentPlugin::get()->isChangeTrackingEnabled()) {
            $widgets[] = ChangeTrackingWidget::class;
        }

        return $widgets;
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 2,
        ];
    }
}
