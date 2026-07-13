<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Presentation\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;

final class CreateSavedConfigurationRequest extends FormRequest
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
            'productId' => ['required', 'string', 'ulid'],
            'selection' => ['present', 'array', 'max:100'],
            'selection.*' => ['nullable'],
            'selection.*.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function productId(): string
    {
        return $this->string('productId')->toString();
    }

    public function toSelection(): ConfigurationSelection
    {
        /** @var array<string, mixed> $selection */
        $selection = $this->validated('selection');

        return ConfigurationSelection::fromArray($selection);
    }
}
