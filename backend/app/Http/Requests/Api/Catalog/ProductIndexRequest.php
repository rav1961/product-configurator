<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Catalog;

use App\Data\Catalog\ProductIndexFilters;
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
                'max:'.ProductIndexFilters::MAX_PER_PAGE,
            ],
            'q' => [
                'sometimes',
                'string',
                'min:3',
                'max:100',
            ],
        ];
    }

    public function filters(): ProductIndexFilters
    {
        return new ProductIndexFilters(
            perPage: $this->perPage(),
            queryText: $this->queryText(),
        );
    }

    private function perPage(): int
    {
        return $this->integer(
            'per_page',
            ProductIndexFilters::DEFAULT_PER_PAGE
        );
    }

    private function queryText(): ?string
    {
        $value = $this->string('q')->toString();
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return Str::limit($value, 100, '');
    }
}
