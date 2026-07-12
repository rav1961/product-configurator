<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Presentation\Filament\Concerns\FilamentRulesContext;
use Modules\Shared\Domain\Enums\MoneyOperation;
use Modules\Shared\Domain\ValueObjects\Money;
use Modules\Shared\Domain\ValueObjects\MoneyAdjustment;
use Modules\Shared\Presentation\Filament\Forms\MoneyAmountInput;

final class RuleActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rules_engine.navigation.actions');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label(__('rules_engine.fields.type'))
                ->options(self::actionTypeOptions())
                ->required()
                ->live()
                ->native(false),
            MoneyAmountInput::make('payload_amount', __('rules_engine.fields.payload_amount'))
                ->required(fn (Get $get): bool => self::requiresAmount($get('type')))
                ->visible(fn (Get $get): bool => self::requiresAmount($get('type'))),
            Select::make('payload_operation')
                ->label(__('rules_engine.fields.payload_operation'))
                ->options(self::moneyOperationOptions())
                ->default(MoneyOperation::Add->value)
                ->required(fn (Get $get): bool => $get('type') === RuleActionType::AddModifier->value)
                ->visible(fn (Get $get): bool => $get('type') === RuleActionType::AddModifier->value)
                ->native(false),
            TextInput::make('payload_label')
                ->label(__('rules_engine.fields.payload_label'))
                ->maxLength(255)
                ->visible(fn (Get $get): bool => $get('type') === RuleActionType::AddModifier->value),
            Select::make('payload_attribute_id')
                ->label(__('rules_engine.fields.payload_attribute'))
                ->options(fn (): array => $this->attributePublicIdOptions())
                ->searchable()
                ->required(fn (Get $get): bool => $get('type') === RuleActionType::ExcludeOption->value)
                ->visible(fn (Get $get): bool => $get('type') === RuleActionType::ExcludeOption->value)
                ->native(false),
            TextInput::make('payload_value')
                ->label(__('rules_engine.fields.payload_value'))
                ->maxLength(255)
                ->required(fn (Get $get): bool => $get('type') === RuleActionType::ExcludeOption->value)
                ->visible(fn (Get $get): bool => $get('type') === RuleActionType::ExcludeOption->value),
            Select::make('payload_level')
                ->label(__('rules_engine.fields.payload_level'))
                ->options(self::messageLevelOptions())
                ->required(fn (Get $get): bool => $get('type') === RuleActionType::AddMessage->value)
                ->visible(fn (Get $get): bool => $get('type') === RuleActionType::AddMessage->value)
                ->native(false),
            Textarea::make('payload_message')
                ->label(__('rules_engine.fields.payload_message'))
                ->rows(3)
                ->maxLength(500)
                ->required(fn (Get $get): bool => $get('type') === RuleActionType::AddMessage->value)
                ->visible(fn (Get $get): bool => $get('type') === RuleActionType::AddMessage->value),
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
            ->columns([
                TextColumn::make('type')
                    ->label(__('rules_engine.fields.type'))
                    ->getStateUsing(fn (RuleAction $record): string => $record->type->label())
                    ->sortable(),
                TextColumn::make('payload_summary')
                    ->label(__('rules_engine.fields.payload'))
                    ->getStateUsing(fn (RuleAction $record): string => self::formatPayloadSummary($record))
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('position')
                    ->label(__('rules_engine.fields.position'))
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data): Model {
                        $owner = $this->getOwnerRecord();
                        assert($owner instanceof Rule);

                        return RuleAction::query()->create([
                            'public_id' => (string) Str::ulid(),
                            'rule_id' => $owner->getKey(),
                            'type' => $data['type'],
                            'payload' => self::buildPayload($data),
                            'position' => (int) $data['position'],
                        ]);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('rules_engine.actions.edit_action'))
                    ->fillForm(fn (RuleAction $record): array => self::payloadToForm($record))
                    ->using(function (RuleAction $record, array $data): RuleAction {
                        $record->update([
                            'type' => $data['type'],
                            'payload' => self::buildPayload($data),
                            'position' => (int) $data['position'],
                        ]);

                        return $record;
                    }),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('rules_engine.actions.delete_action')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('rules_engine.fields.actions'));
    }

    /**
     * @return array<string, string>
     */
    private function attributePublicIdOptions(): array
    {
        $productId = FilamentRulesContext::productId($this);

        return Attribute::query()
            ->whereHas('step', fn (Builder $query): Builder => $query->where('product_id', $productId))
            ->with('step')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Attribute $attribute): array => [
                $attribute->public_id => sprintf(
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
    private static function actionTypeOptions(): array
    {
        $options = [];

        foreach (RuleActionType::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private static function moneyOperationOptions(): array
    {
        $options = [];

        foreach (MoneyOperation::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private static function messageLevelOptions(): array
    {
        return [
            'info' => __('rules_engine.message_level.info'),
            'warning' => __('rules_engine.message_level.warning'),
            'error' => __('rules_engine.message_level.error'),
        ];
    }

    private static function requiresAmount(mixed $type): bool
    {
        if (! is_string($type) || $type === '') {
            return false;
        }

        return in_array($type, [
            RuleActionType::AddModifier->value,
            RuleActionType::SetOverride->value,
        ], true);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function buildPayload(array $data): array
    {
        $type = RuleActionType::from((string) $data['type']);

        return match ($type) {
            RuleActionType::AddModifier => array_filter([
                ...(new MoneyAdjustment(
                    MoneyAmountInput::parseOrFail('payload_amount', (string) $data['payload_amount']),
                    MoneyOperation::from((string) ($data['payload_operation'] ?? MoneyOperation::Add->value)),
                ))->toPayload(),
                'label' => isset($data['payload_label']) && $data['payload_label'] !== ''
                    ? (string) $data['payload_label']
                    : null,
            ], static fn (mixed $value): bool => $value !== null),
            RuleActionType::SetOverride => MoneyAmountInput::parseOrFail(
                'payload_amount',
                (string) $data['payload_amount'],
            )->toPayloadAmount(),
            RuleActionType::ExcludeOption => [
                'attribute_id' => (string) $data['payload_attribute_id'],
                'value' => (string) $data['payload_value'],
            ],
            RuleActionType::AddMessage => [
                'level' => (string) $data['payload_level'],
                'message' => (string) $data['payload_message'],
            ],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private static function payloadToForm(RuleAction $record): array
    {
        $payload = $record->payload;

        return [
            'type' => $record->type->value,
            'position' => $record->position,
            'payload_amount' => self::formatAmountForForm($record),
            'payload_operation' => $payload[MoneyAdjustment::OPERATION_KEY] ?? MoneyOperation::Add->value,
            'payload_label' => $payload['label'] ?? null,
            'payload_attribute_id' => $payload['attribute_id'] ?? null,
            'payload_value' => $payload['value'] ?? null,
            'payload_level' => $payload['level'] ?? null,
            'payload_message' => $payload['message'] ?? null,
        ];
    }

    private static function formatAmountForForm(RuleAction $record): ?string
    {
        return match ($record->type) {
            RuleActionType::AddModifier => MoneyAdjustment::tryFromPayload($record->payload)?->money->toDecimal(),
            RuleActionType::SetOverride => Money::tryFromPayloadAmount($record->payload)?->toDecimal(),
            default => null,
        };
    }

    private static function formatPayloadSummary(RuleAction $record): string
    {
        $payload = $record->payload;

        return match ($record->type) {
            RuleActionType::AddModifier => self::formatAdjustmentSummary($payload),
            RuleActionType::SetOverride => Money::tryFromPayloadAmount($payload)?->toDecimal() ?? '—',
            RuleActionType::ExcludeOption => sprintf(
                '%s = %s',
                $payload['attribute_id'] ?? '—',
                $payload['value'] ?? '—',
            ),
            RuleActionType::AddMessage => sprintf(
                '[%s] %s',
                $payload['level'] ?? '—',
                $payload['message'] ?? '—',
            ),
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function formatAdjustmentSummary(array $payload): string
    {
        $adjustment = MoneyAdjustment::tryFromPayload($payload);

        if ($adjustment === null) {
            return '—';
        }

        $label = isset($payload['label']) && $payload['label'] !== ''
            ? ' · '.$payload['label']
            : '';

        return sprintf(
            '%s %s%s',
            $adjustment->displayPrefix(),
            $adjustment->money->toDecimal(),
            $label,
        );
    }
}
