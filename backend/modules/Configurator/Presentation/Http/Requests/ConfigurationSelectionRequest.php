<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;

final class ConfigurationSelectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'selection' => ['present', 'array', 'max:100'],
            'selection.*' => ['nullable'],
            'selection.*.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toSelections(): ConfigurationSelection
    {
        $selection = $this->validated('selection');

        return ConfigurationSelection::fromArray($selection);
    }
}
