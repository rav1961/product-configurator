<?php

declare(strict_types=1);

namespace Modules\Shared\Presentation\Filament\Forms;

use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;
use Modules\Shared\Domain\Exceptions\InvalidMoneyException;
use Modules\Shared\Domain\ValueObjects\Money;

final class MoneyAmountInput
{
    public static function make(string $name, ?string $label = null): TextInput
    {
        return TextInput::make($name)
            ->label($label ?? __('shared.money.field_label'))
            ->maxLength(32)
            ->rules(['regex:/'.Money::userInputPattern().'/'])
            ->helperText(__('shared.money.input_hint'));
    }

    public static function parseOrFail(string $field, string $input): Money
    {
        try {
            return Money::fromUserInput($input);
        } catch (InvalidMoneyException) {
            throw ValidationException::withMessages([
                $field => __('shared.money.invalid_input'),
            ]);
        }
    }
}
