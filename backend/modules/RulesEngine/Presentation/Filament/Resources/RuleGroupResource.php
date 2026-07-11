<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Presentation\Filament\RelationManagers\RuleConditionsRelationManager;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleGroupResource\Pages\EditRuleGroup;

class RuleGroupResource extends Resource
{
    protected static ?string $model = RuleGroup::class;

    protected static ?string $parentResource = RuleResource::class;

    protected static ?string $recordTitleAttribute = 'public_id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    public static function getModelLabel(): string
    {
        return __('rules_engine.label.group.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('rules_engine.label.group.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('conditions_match_mode')
                ->label(__('rules_engine.fields.conditions_match_mode'))
                ->options(self::matchModeOptions())
                ->required()
                ->native(false),
            TextInput::make('position')
                ->label(__('rules_engine.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RuleConditionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditRuleGroup::route('/{record}/edit'),
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
