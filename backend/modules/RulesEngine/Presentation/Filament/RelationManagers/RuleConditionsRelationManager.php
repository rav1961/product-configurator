<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Presentation\Filament\Concerns\FilamentRulesContext;
use Modules\Shared\Domain\Enums\SelectionCondition;

final class RuleConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rules_engine.navidation.conditions');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('source_attribute_id')
                ->label(__('rules_engine.fields.sourc_attribute'))
                ->options(fn (): array => $this->productAttributeOptions())
                ->searchable()
                ->required()
                ->native(false),
            Select::make('condition')
                ->label(__('rules_engine.fields.condition'))
                ->options(self::conditionOptions())
                ->required()
                ->live()
                ->native(false),
            TextInput::make('condition_value')
                ->label(__('rules_engine.fields.condition_value'))
                ->maxLength(255)
                ->required(fn (Get $get): bool => self::conditionRequiresValue($get('condition')))
                ->visible(fn (Get $get): bool => self::conditionRequiresValue($get('condition'))),
            TextInput::make('position')
                ->label(__('rules_engine.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['sourceAttribute.step']))
            ->columns([
                TextColumn::make('sourceAttribute.name')
                    ->label(__('rules_engine.fields.source_attribute'))
                    ->description(fn (RuleCondition $record): string => $record->sourceAttribute->step->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('condition')
                    ->label(__('rules_engine.fields.condition'))
                    ->formatStateUsing(fn (SelectionCondition $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('condition_value')
                    ->label(__('rules_engine.fields.condition_value'))
                    ->placeholder('—')
                    ->toggleable(),
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
                    ->tooltip(__('rules_engine.actions.edit_condition')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('rules_engine.actions.delete_condition')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('rules_engine.fields.actions'));
    }

    /**
     * @return array<int, string>
     */
    private function productAttributeOptions(): array
    {
        $productId = FilamentRulesContext::productId($this);

        return Attribute::query()
            ->whereHas('step', fn (Builder $query): Builder => $query->where('product_id', $productId))
            ->with('step')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Attribute $attribute): array => [
                $attribute->getKey() => sprintf(
                    '%s → %s (%s)',
                    $attribute->step->name,
                    $attribute->name,
                    $attribute->key,
                ),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private static function conditionOptions(): array
    {
        $options = [];

        foreach (SelectionCondition::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    private static function conditionRequiresValue(mixed $condition): bool
    {
        if (! is_string($condition) || $condition === '') {
            return false;
        }

        return SelectionCondition::tryFrom($condition)?->requiredValue() ?? false;
    }
}
