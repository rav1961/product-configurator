<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Catalog;

use App\Actions\Catalog\ListActiveProductsAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

final class ProductIndexRequest extends FormRequest
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
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:'.ListActiveProductsAction::MAX_PER_PAGE,
            ],
            'q' => [
                'sometimes',
                'string',
                'min:3',
                'max:100',
            ],
        ];
    }

    public function perPage(): int
    {
        return $this->integer(
            'per_page',
            ListActiveProductsAction::DEFAULT_PER_PAGE
        );
    }

    public function queryText(): ?string
    {
        $value = $this->string('q')->toString();
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return Str::limit($value, 100, '');
    }
}
