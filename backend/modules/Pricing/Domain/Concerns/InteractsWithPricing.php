<?php

namespace Modules\Pricing\Domain\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Pricing\Domain\Models\ProductPrice;

trait InteractsWithPricing
{
    /**
     * @return HasOne<ProductPrice, $this>
     */
    public function price(): HasOne
    {
        return $this->hasOne(ProductPrice::class);
    }
}
