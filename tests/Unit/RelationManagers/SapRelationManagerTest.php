<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Concerns\SapRelationManager;

it('extends filament relation manager', function () {
    expect(is_subclass_of(SapRelationManager::class, \Filament\Resources\RelationManagers\RelationManager::class))
        ->toBeTrue();
});

it('has a dummy relationship name', function () {
    $reflection = new ReflectionClass(SapRelationManager::class);
    $property = $reflection->getProperty('relationship');

    expect($property->getDefaultValue())->toBe('sapRelation');
});

it('always allows viewing for record', function () {
    $model = new class extends \Illuminate\Database\Eloquent\Model {};
    $pageClass = \Filament\Resources\Pages\ViewRecord::class;

    expect(SapRelationManager::canViewForRecord($model, $pageClass))->toBeTrue();
});
