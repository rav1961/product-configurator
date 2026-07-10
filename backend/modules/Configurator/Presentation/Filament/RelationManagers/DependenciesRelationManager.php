<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\RelationManagers;

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
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Shared\Domain\Enums\SelectionCondition;

final class DependenciesRelationManager extends RelationManager
{
    protected static string $relationship = 'dependencies';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof Product || ! $ownerRecord->isConfigurable()) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('configurator.navigation.dependencies');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('source_attribute_id')
                ->label(__('configurator.fields.source_attribute'))
                ->options(fn (): array => $this->productAttributeOptions())
                ->searchable()
                ->required()
                ->live()
                ->native(false),
            Select::make('target_attribute_id')
                ->label(__('configurator.fields.target_attribute'))
                ->options(fn (): array => $this->productAttributeOptions())
                ->searchable()
                ->required()
                ->different('source_attribute_id')
                ->native(false),
            Select::make('condition')
                ->label(__('configurator.fields.condition'))
                ->options(self::conditionOptions())
                ->required()
                ->live()
                ->native(false),
            TextInput::make('condition_value')
                ->label(__('configurator.fields.condition_value'))
                ->maxLength(255)
                ->required(fn (Get $get): bool => self::conditionRequiresValue($get('condition')))
                ->visible(fn (Get $get): bool => self::conditionRequiresValue($get('condition'))),
            Select::make('action')
                ->label(__('configurator.fields.action'))
                ->options(self::actionOptions())
                ->required()
                ->native(false),
            TextInput::make('position')
                ->label(__('configurator.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'sourceAttribute.step',
                'targetAttribute.step',
            ]))
            ->columns([
                TextColumn::make('sourceAttribute.name')
                    ->label(__('configurator.fields.source_attribute'))
                    ->description(fn (Dependency $record): string => $record->sourceAttribute->step->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('condition')
                    ->label(__('configurator.fields.condition'))
                    ->formatStateUsing(fn (SelectionCondition $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('condition_value')
                    ->label(__('configurator.fields.condition_value'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('targetAttribute.name')
                    ->label(__('configurator.fields.target_attribute'))
                    ->description(fn (Dependency $record): string => $record->targetAttribute->step->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->label(__('configurator.fields.action'))
                    ->formatStateUsing(fn (DependencyAction $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('position')
                    ->label(__('configurator.fields.position'))
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
                    ->tooltip(__('configurator.actions.edit_dependency')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('configurator.actions.delete_dependency')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('configurator.fields.actions'));
    }

    /**
     * @return array<int, string>
     */
    private function productAttributeOptions(): array
    {
        $owner = $this->getOwnerRecord();
        assert($owner instanceof Product);

        return Attribute::query()
            ->whereHas('step', fn (Builder $query): Builder => $query->where('product_id', $owner->getKey()))
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

    /**
     * @return array<string, string>
     */
    private static function actionOptions(): array
    {
        $options = [];

        foreach (DependencyAction::cases() as $case) {
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
