<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleResource;

final class RulesRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    protected static ?string $relatedResource = RuleResource::class;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof Product || ! $ownerRecord->isConfigurable()) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rules_engine.navigation.rules');
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();

        assert($owner instanceof Product);

        $productId = $owner->getKey();

        $editUrl = fn (Rule $record): string => RuleResource::getUrl('edit', [
            'product' => $productId,
            'record' => $record,
        ]);

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('rules_engine.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('groups_match_mode')
                    ->label(__('rules_engine.fields.groups_match_mode'))
                    ->getStateUsing(fn (Rule $record): string => $record->groups_match_mode->label())
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('rules_engine.fields.is_active'))
                    ->boolean(),
                TextColumn::make('position')
                    ->label(__('rules_engine.fields.position'))
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('rules_engine.actions.edit_rule'))
                    ->url($editUrl),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('rules_engine.actions.delete_rule')),
            ], RecordActionsPosition::AfterColumns)
            ->recordUrl($editUrl);
    }
}
