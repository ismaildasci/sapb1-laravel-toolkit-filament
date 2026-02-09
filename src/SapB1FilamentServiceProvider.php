<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use SapB1\Toolkit\Events\ApprovalCompleted;
use SapB1\Toolkit\Events\ApprovalRequested;
use SapB1\Toolkit\Events\DocumentCancelled;
use SapB1\Toolkit\Events\DocumentClosed;
use SapB1\Toolkit\Events\DocumentCreated;
use SapB1\Toolkit\Events\PaymentReceived;
use SapB1\Toolkit\Filament\Listeners\ApprovalNotificationListener;
use SapB1\Toolkit\Filament\Listeners\DocumentNotificationListener;
use SapB1\Toolkit\Filament\Listeners\PaymentNotificationListener;
use SapB1\Toolkit\Filament\Widgets\AuditActivityWidget;
use SapB1\Toolkit\Filament\Widgets\CacheStatsWidget;
use SapB1\Toolkit\Filament\Widgets\ChangeTrackingWidget;
use SapB1\Toolkit\Filament\Widgets\SyncOverviewWidget;
use SapB1\Toolkit\Filament\Widgets\SystemHealthWidget;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SapB1FilamentServiceProvider extends PackageServiceProvider
{
    public static string $name = 'sapb1-filament';

    public static string $viewNamespace = 'sapb1-filament';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews(static::$viewNamespace)
            ->hasTranslations()
            ->hasMigration('create_sap_sync_history_table');
    }

    public function packageBooted(): void
    {
        $this->registerLivewireComponents();
        $this->registerAssets();
        $this->registerEventListeners();
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('sapb1-system-health-widget', SystemHealthWidget::class);
        Livewire::component('sapb1-audit-activity-widget', AuditActivityWidget::class);
        Livewire::component('sapb1-sync-overview-widget', SyncOverviewWidget::class);
        Livewire::component('sapb1-cache-stats-widget', CacheStatsWidget::class);
        Livewire::component('sapb1-change-tracking-widget', ChangeTrackingWidget::class);
    }

    protected function registerEventListeners(): void
    {
        if (! config('sapb1-filament.notifications.enabled', false)) {
            return;
        }

        Event::listen(DocumentCreated::class, [DocumentNotificationListener::class, 'handleCreated']);
        Event::listen(DocumentClosed::class, [DocumentNotificationListener::class, 'handleClosed']);
        Event::listen(DocumentCancelled::class, [DocumentNotificationListener::class, 'handleCancelled']);
        Event::listen(PaymentReceived::class, [PaymentNotificationListener::class, 'handle']);
        Event::listen(ApprovalRequested::class, [ApprovalNotificationListener::class, 'handleRequested']);
        Event::listen(ApprovalCompleted::class, [ApprovalNotificationListener::class, 'handleCompleted']);
    }

    protected function registerAssets(): void
    {
        FilamentAsset::register(
            assets: [
                // Custom CSS if needed
                // Css::make('sapb1-filament-styles', __DIR__.'/../resources/dist/sapb1-filament.css'),
            ],
            package: 'ismaildasci/laravel-sapb1-toolkit-filament'
        );
    }
}
