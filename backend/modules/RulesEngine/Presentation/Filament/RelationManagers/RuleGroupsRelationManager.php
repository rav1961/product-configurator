<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Presentation\Filament\Concerns\FilamentRulesContext;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleGroupResource;

final class RuleGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $relatedResource = RuleGroupResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rules_engine.navigation.groups');
    }

    public function table(Table $table): Table
    {
        $ruleId = FilamentRulesContext::ruleId($this);

        $editUrl = fn (RuleGroup $record): string => RuleGroupResource::getUrl('edit', [
            'rule' => $ruleId,
            'record' => $record,
        ]);

        return $table
            ->columns([
                TextColumn::make('conditions_match_mode')
                    ->label(__('rules_engine.fields.conditions_match_mode'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->sortable(),
                TextColumn::make('conditions_count')
                    ->label(__('rules_engine.navigation.conditions'))
                    ->counts('conditions'),
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
                    ->tooltip(__('rules_engine.actions.edit_group'))
                    ->url($editUrl),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('rules_engine.actions.delete_group')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('rules_engine.fields.actions'))
            ->recordUrl($editUrl);
    }
}
