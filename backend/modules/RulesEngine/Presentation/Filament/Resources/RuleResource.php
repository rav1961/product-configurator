<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Presentation\Filament\RelationManagers\RuleActionsRelationManager;
use Modules\RulesEngine\Presentation\Filament\RelationManagers\RuleGroupsRelationManager;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleResource\Pages\EditRule;

final class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static ?string $parentResource = ProductResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    public static function getModelLabel(): string
    {
        return __('rules_engine.label.rule.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('rules_engine.label.rule.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('rules_engine.fields.name'))
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label(__('rules_engine.fields.description'))
                ->rows(3)
                ->maxLength(65535),
            Select::make('groups_match_mode')
                ->label(__('rules_engine.fields.groups_match_mode'))
                ->options(self::matchModeOptions())
                ->required()
                ->native(false),
            TextInput::make('position')
                ->label(__('rules_engine.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label(__('rules_engine.fields.is_active'))
                ->default(true),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RuleGroupsRelationManager::class,
            RuleActionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditRule::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function matchModeOptions(): array
    {
        $options = [];

        foreach (MatchMode::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
