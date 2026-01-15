<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use SapB1\Toolkit\Filament\Resources\AuditLogResource\Pages;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\AuditLog;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::resources.audit_log.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public static function getModelLabel(): string
    {
        return __('sapb1-filament::resources.audit_log.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sapb1-filament::resources.audit_log.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('sapb1-filament::resources.audit_log.sections.details'))
                    ->schema([
                        TextInput::make('entity_type')
                            ->label(__('sapb1-filament::resources.audit_log.fields.entity_type'))
                            ->disabled(),

                        TextInput::make('entity_id')
                            ->label(__('sapb1-filament::resources.audit_log.fields.entity_id'))
                            ->disabled(),

                        TextInput::make('event')
                            ->label(__('sapb1-filament::resources.audit_log.fields.event'))
                            ->disabled(),

                        TextInput::make('user_id')
                            ->label(__('sapb1-filament::resources.audit_log.fields.user_id'))
                            ->disabled(),

                        TextInput::make('tenant_id')
                            ->label(__('sapb1-filament::resources.audit_log.fields.tenant_id'))
                            ->disabled()
                            ->visible(fn () => SapB1FilamentPlugin::get()->isMultiTenantEnabled()),
                    ])
                    ->columns(2),

                Section::make(__('sapb1-filament::resources.audit_log.sections.context'))
                    ->schema([
                        TextInput::make('ip_address')
                            ->label(__('sapb1-filament::resources.audit_log.fields.ip_address'))
                            ->disabled(),

                        TextInput::make('user_agent')
                            ->label(__('sapb1-filament::resources.audit_log.fields.user_agent'))
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('sapb1-filament::resources.audit_log.sections.changes'))
                    ->schema([
                        KeyValue::make('old_values')
                            ->label(__('sapb1-filament::resources.audit_log.fields.old_values'))
                            ->disabled(),

                        KeyValue::make('new_values')
                            ->label(__('sapb1-filament::resources.audit_log.fields.new_values'))
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('entity_type')
                    ->label(__('sapb1-filament::resources.audit_log.fields.entity_type'))
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('entity_id')
                    ->label(__('sapb1-filament::resources.audit_log.fields.entity_id'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('event')
                    ->label(__('sapb1-filament::resources.audit_log.fields.event'))
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user_id')
                    ->label(__('sapb1-filament::resources.audit_log.fields.user_id'))
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('sapb1-filament::resources.audit_log.fields.ip_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tenant_id')
                    ->label(__('sapb1-filament::resources.audit_log.fields.tenant_id'))
                    ->sortable()
                    ->visible(fn () => SapB1FilamentPlugin::get()->isMultiTenantEnabled())
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sapb1-filament::resources.audit_log.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity_type')
                    ->label(__('sapb1-filament::resources.audit_log.filters.entity_type'))
                    ->options(fn () => AuditLog::query()
                        ->distinct()
                        ->pluck('entity_type', 'entity_type')
                        ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('event')
                    ->label(__('sapb1-filament::resources.audit_log.filters.event'))
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('sapb1-filament::resources.audit_log.filters.from')),
                        DatePicker::make('until')
                            ->label(__('sapb1-filament::resources.audit_log.filters.until')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
