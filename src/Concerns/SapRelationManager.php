<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Concerns;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class SapRelationManager extends RelationManager
{
    protected static string $relationship = 'sapRelation';

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract protected function getRecords(
        ?string $search,
        ?string $sortColumn,
        ?string $sortDirection,
    ): array;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    protected function makeTable(): Table
    {
        return Table::make($this)
            ->records(fn (
                ?string $search,
                ?string $sortColumn,
                ?string $sortDirection,
            ): array => $this->getRecords($search, $sortColumn, $sortDirection))
            ->heading(static::getTitle($this->getOwnerRecord(), $this->getPageClass()))
            ->modelLabel(static::getRelationshipTitle())
            ->queryStringIdentifier(Str::lcfirst(class_basename(static::class)));
    }
}
