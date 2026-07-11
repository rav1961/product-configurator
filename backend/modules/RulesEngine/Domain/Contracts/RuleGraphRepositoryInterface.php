<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Models\Rule;

interface RuleGraphRepositoryInterface
{
    /**
     * @return Collection<int, Rule>
     */
    public function buildActiveForProductPublicId(string $productPublicId): Collection;
}
