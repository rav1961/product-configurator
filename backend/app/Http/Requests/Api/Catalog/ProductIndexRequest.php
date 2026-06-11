<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Catalog;

use App\Actions\Catalog\ListActiveProductsAction;
use Illuminate\Foundation\Http\FormRequest;

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
        ];
    }

    public function perPage(): int
    {
        return $this->integer(
            'per_page',
            ListActiveProductsAction::DEFAULT_PER_PAGE
        );
    }
}
