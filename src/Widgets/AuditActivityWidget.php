<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use SapB1\Toolkit\Models\AuditLog;

class AuditActivityWidget extends BaseWidget
{
    protected static ?string $heading = null;

    /**
     * @var array<string, int|null>|int|string
     */
    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 1,
    ];

    protected ?string $pollingInterval = null;

    public function getHeading(): ?string
    {
        return __('sapb1-filament::widgets.audit.heading');
    }

    /**
     * @return Builder<AuditLog>
     */
    protected function getTableQuery(): Builder
    {
        return AuditLog::query()
            ->latest()
            ->limit(config('sapb1-filament.widgets.audit_activity.limit', 10));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('entity_type')
                    ->label(__('sapb1-filament::widgets.audit.entity'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('entity_id')
                    ->label(__('sapb1-filament::widgets.audit.id'))
                    ->searchable(),

                TextColumn::make('event')
                    ->label(__('sapb1-filament::widgets.audit.event'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'info',
                    }),

                TextColumn::make('user_id')
                    ->label(__('sapb1-filament::widgets.audit.user'))
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label(__('sapb1-filament::widgets.audit.time'))
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn (AuditLog $record): string => route('filament.admin.resources.audit-logs.view', $record)),
            ])
            ->paginated(false);
    }
}
