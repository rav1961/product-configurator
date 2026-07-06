<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Presentation\Filament\Concerns\FilamentOwnerContext;
use Modules\Configurator\Presentation\Filament\RelationManagers\AttributeValuesRelationManager;
use Modules\Configurator\Presentation\Filament\Resources\AttributeResource\EditAttribute;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $parentResource = StepResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    public static function getModelLabel(): string
    {
        return __('configurator.label.attribute.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('configurator.label.attribute.plural');
    }

    public static function form(Schema $schema): Schema
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
                    modifyQueryUsing: fn (Builder $query, $livewire): Builder => $query->where(
                        'step_id',
                        FilamentOwnerContext::step($livewire)->getKey(),
                    ),
                ),
            Select::make('type')
                ->label(__('configurator.fields.type'))
                ->options(self::attributeTypeOptions())
                ->required()
                ->live()
                ->native(false),
            Select::make('collection_id')
                ->label(__('configurator.fields.collection'))
                ->helperText(__('configurator.fields.collection_help'))
                ->relationship(
                    name: 'collection',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query, $livewire): Builder => $query->where(
                        'product_id',
                        FilamentOwnerContext::step($livewire)->product_id,
                    ),
                )
                ->visible(function (Get $get): bool {
                    $type = AttributeType::tryFrom((string) $get('type'));

                    return $type?->hasOptions() ?? false;
                })
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

    public static function getRelations(): array
    {
        return [
            AttributeValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditAttribute::route('/{record}/edit'),
        ];
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
