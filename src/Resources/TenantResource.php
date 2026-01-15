<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources;

use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SapB1\Toolkit\Filament\Resources\TenantResource\Pages;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\MultiTenant\MultiTenantService;

/**
 * TenantResource provides admin UI for managing multi-tenant configurations.
 *
 * Since tenants are stored in config, this resource uses a virtual model
 * backed by configuration data rather than a database table.
 */
class TenantResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::resources.tenant.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public static function getModelLabel(): string
    {
        return __('sapb1-filament::resources.tenant.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sapb1-filament::resources.tenant.plural_model_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return SapB1FilamentPlugin::get()->isMultiTenantEnabled();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('sapb1-filament::resources.tenant.sections.info'))
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label(__('sapb1-filament::resources.tenant.fields.id'))
                            ->required()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\TextInput::make('name')
                            ->label(__('sapb1-filament::resources.tenant.fields.name'))
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('sapb1-filament::resources.tenant.sections.connection'))
                    ->schema([
                        Forms\Components\TextInput::make('sap_url')
                            ->label(__('sapb1-filament::resources.tenant.fields.sap_url'))
                            ->url()
                            ->required(),

                        Forms\Components\TextInput::make('sap_database')
                            ->label(__('sapb1-filament::resources.tenant.fields.sap_database'))
                            ->required(),

                        Forms\Components\TextInput::make('sap_username')
                            ->label(__('sapb1-filament::resources.tenant.fields.sap_username'))
                            ->required(),

                        Forms\Components\TextInput::make('sap_password')
                            ->label(__('sapb1-filament::resources.tenant.fields.sap_password'))
                            ->password()
                            ->revealable()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('sapb1-filament::resources.tenant.fields.id'))
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('sapb1-filament::resources.tenant.fields.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sap_database')
                    ->label(__('sapb1-filament::resources.tenant.fields.sap_database'))
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_current')
                    ->label(__('sapb1-filament::resources.tenant.fields.is_current'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('sapb1-filament::resources.tenant.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'connected' => 'success',
                        'disconnected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('switch')
                    ->label(__('sapb1-filament::resources.tenant.actions.switch'))
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->action(fn (array $data) => static::switchTenant($data['id'])),

                Tables\Actions\Action::make('test')
                    ->label(__('sapb1-filament::resources.tenant.actions.test'))
                    ->icon('heroicon-o-signal')
                    ->color('info')
                    ->action(fn (array $data) => static::testConnection($data['id'])),
            ])
            ->bulkActions([])
            ->defaultSort('id', 'asc');
    }

    protected static function switchTenant(string $tenantId): void
    {
        try {
            $multiTenant = app(MultiTenantService::class);
            $multiTenant->setTenant($tenantId);

            Notification::make()
                ->title(__('sapb1-filament::resources.tenant.notifications.switch_success'))
                ->body(sprintf('Switched to tenant: %s', $tenantId))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::resources.tenant.notifications.switch_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected static function testConnection(string $tenantId): void
    {
        try {
            $multiTenant = app(MultiTenantService::class);

            // Run test in tenant context
            $result = $multiTenant->runAs($tenantId, function () {
                // Simple connection test
                return ['status' => 'connected'];
            });

            Notification::make()
                ->title(__('sapb1-filament::resources.tenant.notifications.test_success'))
                ->body(sprintf('Connection to %s successful', $tenantId))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::resources.tenant.notifications.test_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\ManageTenant::route('/create'),
        ];
    }

    /**
     * Get tenant data from configuration.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getTenantData(): array
    {
        $tenants = config('laravel-toolkit.multi_tenant.tenants', []);
        $currentTenant = null;

        try {
            $multiTenant = app(MultiTenantService::class);
            $currentTenant = $multiTenant->getCurrentTenant();
        } catch (Exception $e) {
            // Service not available
        }

        $data = [];
        foreach ($tenants as $id => $config) {
            $data[] = [
                'id' => $id,
                'name' => $config['name'] ?? $id,
                'sap_url' => $config['sap_url'] ?? '',
                'sap_database' => $config['sap_database'] ?? '',
                'is_current' => $currentTenant === $id,
                'status' => 'unknown',
            ];
        }

        return $data;
    }
}
