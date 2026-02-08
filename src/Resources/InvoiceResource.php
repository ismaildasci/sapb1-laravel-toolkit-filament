<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use SapB1\Toolkit\Enums\DocumentStatus;
use Filament\Notifications\Notification;
use SapB1\Toolkit\Filament\Actions\CreateCreditNoteAction;
use SapB1\Toolkit\Services\DocumentActionService;
use SapB1\Toolkit\Filament\Actions\RecordPaymentAction;
use SapB1\Toolkit\Filament\Actions\UploadAttachmentAction;
use SapB1\Toolkit\Filament\Actions\ViewDocumentFlowAction;
use SapB1\Toolkit\Filament\Resources\InvoiceResource\Pages;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\Sales\Invoice;

class InvoiceResource extends Resource
{
    /** @phpstan-ignore-next-line */
    protected static ?string $model = Invoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 51;

    protected static ?string $recordTitleAttribute = 'DocNum';

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::resources.invoice.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public static function getModelLabel(): string
    {
        return __('sapb1-filament::resources.invoice.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sapb1-filament::resources.invoice.plural_model_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return SapB1FilamentPlugin::get()->isInvoiceEnabled();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('sapb1-filament::resources.invoice.sections.basic'))
                    ->schema([
                        TextInput::make('DocNum')
                            ->label(__('sapb1-filament::resources.invoice.fields.doc_num'))
                            ->disabled(),

                        Select::make('CardCode')
                            ->label(__('sapb1-filament::resources.invoice.fields.card_code'))
                            ->relationship('partner', 'CardName')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('DocDate')
                            ->label(__('sapb1-filament::resources.invoice.fields.doc_date'))
                            ->default(now())
                            ->required(),

                        DatePicker::make('DocDueDate')
                            ->label(__('sapb1-filament::resources.invoice.fields.doc_due_date'))
                            ->default(now()->addDays(30))
                            ->required(),

                        TextInput::make('NumAtCard')
                            ->label(__('sapb1-filament::resources.invoice.fields.num_at_card'))
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Section::make(__('sapb1-filament::resources.invoice.sections.lines'))
                    ->schema([
                        Repeater::make('DocumentLines')
                            ->label(__('sapb1-filament::resources.invoice.fields.document_lines'))
                            ->schema([
                                Select::make('ItemCode')
                                    ->label(__('sapb1-filament::resources.invoice.fields.item_code'))
                                    ->relationship('item', 'ItemName')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('Quantity')
                                    ->label(__('sapb1-filament::resources.invoice.fields.quantity'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(0.0001),

                                TextInput::make('Price')
                                    ->label(__('sapb1-filament::resources.invoice.fields.price'))
                                    ->numeric()
                                    ->prefix('TRY')
                                    ->default(0),

                                TextInput::make('WarehouseCode')
                                    ->label(__('sapb1-filament::resources.invoice.fields.warehouse_code'))
                                    ->maxLength(8),

                                TextInput::make('DiscountPercent')
                                    ->label(__('sapb1-filament::resources.invoice.fields.discount_percent'))
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['ItemCode'] ?? null),
                    ]),

                Section::make(__('sapb1-filament::resources.invoice.sections.totals'))
                    ->schema([
                        TextInput::make('VatSum')
                            ->label(__('sapb1-filament::resources.invoice.fields.vat_sum'))
                            ->numeric()
                            ->prefix('TRY')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('DocTotal')
                            ->label(__('sapb1-filament::resources.invoice.fields.doc_total'))
                            ->numeric()
                            ->prefix('TRY')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('PaidToDate')
                            ->label(__('sapb1-filament::resources.invoice.fields.paid_to_date'))
                            ->numeric()
                            ->prefix('TRY')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3)
                    ->visibleOn(['view', 'edit']),

                Section::make(__('sapb1-filament::resources.invoice.sections.notes'))
                    ->schema([
                        Textarea::make('Comments')
                            ->label(__('sapb1-filament::resources.invoice.fields.comments'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('invoice_details')
                    ->tabs([
                        Tabs\Tab::make(__('sapb1-filament::resources.invoice.infolist.details'))
                            ->schema([
                                TextEntry::make('DocNum')
                                    ->label(__('sapb1-filament::resources.invoice.fields.doc_num'))
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large),

                                TextEntry::make('CardCode')
                                    ->label(__('sapb1-filament::resources.invoice.fields.card_code')),

                                TextEntry::make('CardName')
                                    ->label(__('sapb1-filament::resources.invoice.fields.card_name')),

                                TextEntry::make('NumAtCard')
                                    ->label(__('sapb1-filament::resources.invoice.fields.num_at_card'))
                                    ->placeholder('-'),

                                TextEntry::make('DocDate')
                                    ->label(__('sapb1-filament::resources.invoice.fields.doc_date'))
                                    ->date(),

                                TextEntry::make('DocDueDate')
                                    ->label(__('sapb1-filament::resources.invoice.fields.doc_due_date'))
                                    ->date(),

                                TextEntry::make('DocumentStatus')
                                    ->label(__('sapb1-filament::resources.invoice.fields.status'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state instanceof DocumentStatus ? $state->label() : $state)
                                    ->color(fn ($state): string => match (true) {
                                        $state === DocumentStatus::Open || $state === 'bost_Open' => 'success',
                                        $state === DocumentStatus::Closed || $state === 'bost_Close' => 'gray',
                                        $state === DocumentStatus::Cancelled || $state === 'bost_Cancelled' => 'danger',
                                        default => 'warning',
                                    }),

                                TextEntry::make('VatSum')
                                    ->label(__('sapb1-filament::resources.invoice.fields.vat_sum'))
                                    ->money('TRY'),

                                TextEntry::make('DocTotal')
                                    ->label(__('sapb1-filament::resources.invoice.fields.doc_total'))
                                    ->money('TRY')
                                    ->weight('bold'),

                                TextEntry::make('PaidToDate')
                                    ->label(__('sapb1-filament::resources.invoice.fields.paid_to_date'))
                                    ->money('TRY'),

                                TextEntry::make('balance')
                                    ->label(__('sapb1-filament::resources.invoice.fields.balance'))
                                    ->money('TRY')
                                    ->getStateUsing(fn ($record) => ($record->DocTotal ?? 0) - ($record->PaidToDate ?? 0))
                                    ->weight('bold')
                                    ->color(fn ($state): string => $state > 0 ? 'danger' : 'success'),

                                TextEntry::make('Comments')
                                    ->label(__('sapb1-filament::resources.invoice.fields.comments'))
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),

                        Tabs\Tab::make(__('sapb1-filament::resources.invoice.infolist.lines'))
                            ->schema([
                                RepeatableEntry::make('DocumentLines')
                                    ->label('')
                                    ->schema([
                                        TextEntry::make('ItemCode')
                                            ->label(__('sapb1-filament::resources.invoice.fields.item_code')),

                                        TextEntry::make('ItemDescription')
                                            ->label(__('sapb1-filament::resources.invoice.infolist.item_description'))
                                            ->placeholder('-'),

                                        TextEntry::make('Quantity')
                                            ->label(__('sapb1-filament::resources.invoice.fields.quantity'))
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('Price')
                                            ->label(__('sapb1-filament::resources.invoice.fields.price'))
                                            ->money('TRY'),

                                        TextEntry::make('DiscountPercent')
                                            ->label(__('sapb1-filament::resources.invoice.fields.discount_percent'))
                                            ->suffix('%'),

                                        TextEntry::make('WarehouseCode')
                                            ->label(__('sapb1-filament::resources.invoice.fields.warehouse_code'))
                                            ->placeholder('-'),

                                        TextEntry::make('LineTotal')
                                            ->label(__('sapb1-filament::resources.invoice.infolist.line_total'))
                                            ->money('TRY')
                                            ->weight('bold'),
                                    ])
                                    ->columns(7),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DocNum')
                    ->label(__('sapb1-filament::resources.invoice.fields.doc_num'))
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('CardName')
                    ->label(__('sapb1-filament::resources.invoice.fields.card_name'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('DocDate')
                    ->label(__('sapb1-filament::resources.invoice.fields.doc_date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('DocTotal')
                    ->label(__('sapb1-filament::resources.invoice.fields.doc_total'))
                    ->money('TRY')
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('PaidToDate')
                    ->label(__('sapb1-filament::resources.invoice.fields.paid_to_date'))
                    ->money('TRY')
                    ->alignEnd()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label(__('sapb1-filament::resources.invoice.fields.balance'))
                    ->money('TRY')
                    ->alignEnd()
                    ->state(fn ($record) => ($record->DocTotal ?? 0) - ($record->PaidToDate ?? 0))
                    ->color(fn ($state): string => $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('DocumentStatus')
                    ->label(__('sapb1-filament::resources.invoice.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof DocumentStatus ? $state->label() : $state)
                    ->color(fn ($state): string => match (true) {
                        $state === DocumentStatus::Open || $state === 'bost_Open' => 'success',
                        $state === DocumentStatus::Closed || $state === 'bost_Close' => 'gray',
                        $state === DocumentStatus::Cancelled || $state === 'bost_Cancelled' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('DocDueDate')
                    ->label(__('sapb1-filament::resources.invoice.fields.doc_due_date'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('DocumentStatus')
                    ->label(__('sapb1-filament::resources.invoice.filters.status'))
                    ->options([
                        DocumentStatus::Open->value => DocumentStatus::Open->label(),
                        DocumentStatus::Closed->value => DocumentStatus::Closed->label(),
                        DocumentStatus::Cancelled->value => DocumentStatus::Cancelled->label(),
                    ]),

                Tables\Filters\Filter::make('doc_date')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('sapb1-filament::resources.invoice.filters.from')),
                        DatePicker::make('until')
                            ->label(__('sapb1-filament::resources.invoice.filters.until')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->where('DocDate', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->where('DocDate', '<=', $data['until']));
                    }),

                Tables\Filters\TernaryFilter::make('has_balance')
                    ->label(__('sapb1-filament::resources.invoice.filters.has_balance'))
                    ->placeholder(__('sapb1-filament::resources.invoice.filters.all'))
                    ->trueLabel(__('sapb1-filament::resources.invoice.filters.unpaid'))
                    ->falseLabel(__('sapb1-filament::resources.invoice.filters.paid')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->DocumentStatus === DocumentStatus::Open ||
                        $record->DocumentStatus === 'bost_Open'),
                CreateCreditNoteAction::make(),
                RecordPaymentAction::make(),
                ViewDocumentFlowAction::make(),
                UploadAttachmentAction::make()
                    ->entityEndpoint('Invoices')
                    ->entityKeyField('DocEntry'),
                Tables\Actions\Action::make('cancel')
                    ->label(__('sapb1-filament::resources.invoice.actions.cancel'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('sapb1-filament::resources.invoice.actions.cancel_confirm_title'))
                    ->modalDescription(__('sapb1-filament::resources.invoice.actions.cancel_confirm_description'))
                    ->action(fn ($record) => $record->cancel())
                    ->visible(fn ($record) => $record->DocumentStatus === DocumentStatus::Open ||
                        $record->DocumentStatus === 'bost_Open'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->DocumentStatus === DocumentStatus::Open ||
                        $record->DocumentStatus === 'bost_Open'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_cancel')
                        ->label(__('sapb1-filament::resources.invoice.actions.bulk_cancel'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('sapb1-filament::resources.invoice.actions.bulk_cancel_confirm_title'))
                        ->modalDescription(__('sapb1-filament::resources.invoice.actions.bulk_cancel_confirm_description'))
                        ->action(function ($records): void {
                            /** @var DocumentActionService $service */
                            $service = app(DocumentActionService::class);
                            $docEntries = $records->pluck('DocEntry')->toArray();
                            $service->cancelInvoices($docEntries);

                            Notification::make()
                                ->success()
                                ->title(__('sapb1-filament::resources.invoice.notifications.bulk_cancel_complete'))
                                ->body(__('sapb1-filament::resources.invoice.notifications.bulk_action_count', [
                                    'count' => count($docEntries),
                                ]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('DocNum', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
