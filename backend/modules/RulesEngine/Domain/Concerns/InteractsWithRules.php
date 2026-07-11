<?php

namespace Modules\RulesEngine\Domain\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\RulesEngine\Domain\Models\Rule;

trait InteractsWithRules
{
    /**
     * @return HasMany<Rule, $this>
     */
    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }
}
