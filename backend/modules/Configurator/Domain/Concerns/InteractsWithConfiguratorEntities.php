<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;

/** @mixin Product */
trait InteractsWithConfiguratorEntities
{
    /**
     * @return HasMany<Step, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    /**
     * @return HasMany<AttributeCollection, $this>
     */
    public function attributeCollections(): HasMany
    {
        return $this->hasMany(AttributeCollection::class);
    }

    /**
     * @return HasMany<Dependency, $this>
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(Dependency::class);
    }
}
