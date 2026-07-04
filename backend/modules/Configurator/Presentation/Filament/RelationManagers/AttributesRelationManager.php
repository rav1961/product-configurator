<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;

final class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('configurator.navigation.attributes');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('configurator.fields.name'))
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(static function (string $operation, ?string $state, Set $set): void {
                    if ($operation !== 'create') {
                        return;
                    }

                    $set('key', Str::slug((string) $state));
                }),
            TextInput::make('key')
                ->label(__('configurator.fields.key'))
                ->required()
                ->maxLength(255)
                ->scopedUnique(
                    Attribute::class,
                    'key',
                    modifyQueryUsing: fn (Builder $query): Builder => $query->where(
                        'step_id',
                        $this->getOwnerRecord()->getKey(),
                    ),
                ),
            Select::make('type')
                ->label(__('configurator.fields.type'))
                ->options(self::attributeTypeOptions())
                ->required()
                ->native(false),
            Select::make('collection_id')
                ->label(__('configurator.fields.collection'))
                ->helperText(__('configurator.fields.collection_help'))
                ->relationship(
                    name: 'collection',
                    titleAttribute: 'name',
                    modifyQueryUsing: function (Builder $query): Builder {
                        $owner = $this->getOwnerRecord();
                        assert($owner instanceof Step);

                        return $query->where('product_id', $owner->product_id);
                    },
                )
                ->searchable()
                ->preload()
                ->nullable(),
            Toggle::make('is_required')
                ->label(__('configurator.fields.is_required'))
                ->default(false),
            TextInput::make('position')
                ->label(__('configurator.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label(__('configurator.fields.name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('key')
                ->label(__('configurator.fields.key'))
                ->toggleable(),
            TextColumn::make('type')
                ->label(__('configurator.fields.type'))
                ->formatStateUsing(fn (AttributeType $state): string => $state->label())
                ->sortable(),
            IconColumn::make('is_required')
                ->label(__('configurator.fields.is_required'))
                ->boolean(),
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
                    ->tooltip(__('configurator.actions.edit_attribute')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('configurator.actions.delete_attribute')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('configurator.fields.actions'));
    }

    /**
     * @return array<string, string>
     */
    private static function attributeTypeOptions(): array
    {
        $options = [];

        foreach (AttributeType::cases() as $type) {
            $options[$type->value] = $type->label();
        }

        return $options;
    }
}
