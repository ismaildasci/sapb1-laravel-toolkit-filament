<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use SapB1\Toolkit\Enums\CardType;
use SapB1\Toolkit\Filament\Actions\UploadAttachmentAction;
use SapB1\Toolkit\Filament\Resources\PartnerResource\Pages;
use SapB1\Toolkit\Filament\Resources\PartnerResource\Widgets;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\BusinessPartner\Partner;
use SapB1\Toolkit\Services\BatchService;

class PartnerResource extends Resource
{
    /** @phpstan-ignore-next-line */
    protected static ?string $model = Partner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'CardName';

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::resources.partner.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public static function getModelLabel(): string
    {
        return __('sapb1-filament::resources.partner.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sapb1-filament::resources.partner.plural_model_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return SapB1FilamentPlugin::get()->isPartnerEnabled();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('sapb1-filament::resources.partner.sections.basic'))
                    ->schema([
                        TextInput::make('CardCode')
                            ->label(__('sapb1-filament::resources.partner.fields.card_code'))
                            ->required()
                            ->maxLength(15)
                            ->disabled(fn ($record) => $record !== null),

                        TextInput::make('CardName')
                            ->label(__('sapb1-filament::resources.partner.fields.card_name'))
                            ->required()
                            ->maxLength(100),

                        Select::make('CardType')
                            ->label(__('sapb1-filament::resources.partner.fields.card_type'))
                            ->options([
                                CardType::Customer->value => CardType::Customer->label(),
                                CardType::Supplier->value => CardType::Supplier->label(),
                                CardType::Lead->value => CardType::Lead->label(),
                            ])
                            ->required(),

                        TextInput::make('GroupCode')
                            ->label(__('sapb1-filament::resources.partner.fields.group_code'))
                            ->numeric(),
                    ])
                    ->columns(2),

                Section::make(__('sapb1-filament::resources.partner.sections.contact'))
                    ->schema([
                        TextInput::make('Phone1')
                            ->label(__('sapb1-filament::resources.partner.fields.phone1'))
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('Phone2')
                            ->label(__('sapb1-filament::resources.partner.fields.phone2'))
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('Cellular')
                            ->label(__('sapb1-filament::resources.partner.fields.cellular'))
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('Fax')
                            ->label(__('sapb1-filament::resources.partner.fields.fax'))
                            ->maxLength(20),

                        TextInput::make('EmailAddress')
                            ->label(__('sapb1-filament::resources.partner.fields.email'))
                            ->email()
                            ->maxLength(100),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('sapb1-filament::resources.partner.sections.address'))
                    ->schema([
                        Textarea::make('Address')
                            ->label(__('sapb1-filament::resources.partner.fields.address'))
                            ->rows(2)
                            ->columnSpanFull(),

                        TextInput::make('ZipCode')
                            ->label(__('sapb1-filament::resources.partner.fields.zip_code'))
                            ->maxLength(20),

                        TextInput::make('City')
                            ->label(__('sapb1-filament::resources.partner.fields.city'))
                            ->maxLength(100),

                        TextInput::make('Country')
                            ->label(__('sapb1-filament::resources.partner.fields.country'))
                            ->maxLength(3),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make(__('sapb1-filament::resources.partner.sections.finance'))
                    ->schema([
                        TextInput::make('Currency')
                            ->label(__('sapb1-filament::resources.partner.fields.currency'))
                            ->maxLength(3),

                        TextInput::make('FederalTaxID')
                            ->label(__('sapb1-filament::resources.partner.fields.tax_id'))
                            ->maxLength(32),

                        Toggle::make('Valid')
                            ->label(__('sapb1-filament::resources.partner.fields.valid'))
                            ->default(true),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make(__('sapb1-filament::resources.partner.sections.notes'))
                    ->schema([
                        Textarea::make('Notes')
                            ->label(__('sapb1-filament::resources.partner.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('partner_details')
                    ->tabs([
                        Tabs\Tab::make(__('sapb1-filament::resources.partner.infolist.details'))
                            ->schema([
                                TextEntry::make('CardCode')
                                    ->label(__('sapb1-filament::resources.partner.fields.card_code'))
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('CardName')
                                    ->label(__('sapb1-filament::resources.partner.fields.card_name')),

                                TextEntry::make('CardType')
                                    ->label(__('sapb1-filament::resources.partner.fields.card_type'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state instanceof CardType ? $state->label() : $state)
                                    ->color(fn ($state): string => match (true) {
                                        $state === CardType::Customer || $state === 'cCustomer' => 'success',
                                        $state === CardType::Supplier || $state === 'cSupplier' => 'info',
                                        $state === CardType::Lead || $state === 'cLid' => 'warning',
                                        default => 'gray',
                                    }),

                                TextEntry::make('GroupCode')
                                    ->label(__('sapb1-filament::resources.partner.fields.group_code'))
                                    ->placeholder('-'),

                                IconEntry::make('Valid')
                                    ->label(__('sapb1-filament::resources.partner.fields.valid'))
                                    ->boolean(),
                            ])
                            ->columns(3),

                        Tabs\Tab::make(__('sapb1-filament::resources.partner.infolist.contact'))
                            ->schema([
                                TextEntry::make('Phone1')
                                    ->label(__('sapb1-filament::resources.partner.fields.phone1'))
                                    ->placeholder('-'),

                                TextEntry::make('Phone2')
                                    ->label(__('sapb1-filament::resources.partner.fields.phone2'))
                                    ->placeholder('-'),

                                TextEntry::make('Cellular')
                                    ->label(__('sapb1-filament::resources.partner.fields.cellular'))
                                    ->placeholder('-'),

                                TextEntry::make('Fax')
                                    ->label(__('sapb1-filament::resources.partner.fields.fax'))
                                    ->placeholder('-'),

                                TextEntry::make('EmailAddress')
                                    ->label(__('sapb1-filament::resources.partner.fields.email'))
                                    ->copyable()
                                    ->placeholder('-'),

                                TextEntry::make('Address')
                                    ->label(__('sapb1-filament::resources.partner.fields.address'))
                                    ->placeholder('-')
                                    ->columnSpanFull(),

                                TextEntry::make('City')
                                    ->label(__('sapb1-filament::resources.partner.fields.city'))
                                    ->placeholder('-'),

                                TextEntry::make('ZipCode')
                                    ->label(__('sapb1-filament::resources.partner.fields.zip_code'))
                                    ->placeholder('-'),

                                TextEntry::make('Country')
                                    ->label(__('sapb1-filament::resources.partner.fields.country'))
                                    ->placeholder('-'),
                            ])
                            ->columns(3),

                        Tabs\Tab::make(__('sapb1-filament::resources.partner.infolist.finance'))
                            ->schema([
                                TextEntry::make('Currency')
                                    ->label(__('sapb1-filament::resources.partner.fields.currency'))
                                    ->placeholder('-'),

                                TextEntry::make('FederalTaxID')
                                    ->label(__('sapb1-filament::resources.partner.fields.tax_id'))
                                    ->placeholder('-'),

                                TextEntry::make('CurrentAccountBalance')
                                    ->label(__('sapb1-filament::resources.partner.fields.balance'))
                                    ->money('TRY')
                                    ->weight('bold')
                                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                                TextEntry::make('OpenOrdersBalance')
                                    ->label(__('sapb1-filament::resources.partner.infolist.open_orders_balance'))
                                    ->money('TRY')
                                    ->placeholder('0.00'),

                                TextEntry::make('OpenDeliveryNotesBalance')
                                    ->label(__('sapb1-filament::resources.partner.infolist.open_delivery_balance'))
                                    ->money('TRY')
                                    ->placeholder('0.00'),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('CardCode')
                    ->label(__('sapb1-filament::resources.partner.fields.card_code'))
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('CardName')
                    ->label(__('sapb1-filament::resources.partner.fields.card_name'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('CardType')
                    ->label(__('sapb1-filament::resources.partner.fields.card_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof CardType ? $state->label() : $state)
                    ->color(fn ($state): string => match (true) {
                        $state === CardType::Customer || $state === 'cCustomer' => 'success',
                        $state === CardType::Supplier || $state === 'cSupplier' => 'info',
                        $state === CardType::Lead || $state === 'cLid' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('City')
                    ->label(__('sapb1-filament::resources.partner.fields.city'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('Phone1')
                    ->label(__('sapb1-filament::resources.partner.fields.phone1'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('EmailAddress')
                    ->label(__('sapb1-filament::resources.partner.fields.email'))
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('CurrentAccountBalance')
                    ->label(__('sapb1-filament::resources.partner.fields.balance'))
                    ->money('TRY')
                    ->alignEnd()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('Valid')
                    ->label(__('sapb1-filament::resources.partner.fields.valid'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('CardType')
                    ->label(__('sapb1-filament::resources.partner.filters.card_type'))
                    ->options([
                        CardType::Customer->value => CardType::Customer->label(),
                        CardType::Supplier->value => CardType::Supplier->label(),
                        CardType::Lead->value => CardType::Lead->label(),
                    ]),

                Tables\Filters\TernaryFilter::make('Valid')
                    ->label(__('sapb1-filament::resources.partner.filters.valid'))
                    ->placeholder(__('sapb1-filament::resources.partner.filters.all'))
                    ->trueLabel(__('sapb1-filament::resources.partner.filters.active'))
                    ->falseLabel(__('sapb1-filament::resources.partner.filters.inactive')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                UploadAttachmentAction::make()
                    ->entityEndpoint('BusinessPartners')
                    ->entityKeyField('CardCode'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label(__('sapb1-filament::resources.partner.actions.bulk_activate'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            /** @var BatchService $service */
                            $service = app(BatchService::class);
                            $updates = $records->map(fn ($record) => [
                                'CardCode' => $record->CardCode,
                                'Valid' => 'tYES',
                            ])->toArray();
                            $service->updatePartners($updates);

                            Notification::make()
                                ->success()
                                ->title(__('sapb1-filament::resources.partner.notifications.bulk_activate_complete'))
                                ->body(__('sapb1-filament::resources.partner.notifications.bulk_action_count', [
                                    'count' => count($updates),
                                ]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label(__('sapb1-filament::resources.partner.actions.bulk_deactivate'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            /** @var BatchService $service */
                            $service = app(BatchService::class);
                            $updates = $records->map(fn ($record) => [
                                'CardCode' => $record->CardCode,
                                'Valid' => 'tNO',
                            ])->toArray();
                            $service->updatePartners($updates);

                            Notification::make()
                                ->success()
                                ->title(__('sapb1-filament::resources.partner.notifications.bulk_deactivate_complete'))
                                ->body(__('sapb1-filament::resources.partner.notifications.bulk_action_count', [
                                    'count' => count($updates),
                                ]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('CardCode', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\PartnerStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'view' => Pages\ViewPartner::route('/{record}'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
