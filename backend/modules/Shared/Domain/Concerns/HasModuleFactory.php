<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * @template TFactory of Factory
 */
trait HasModuleFactory
{
    /** @use HasFactory<TFactory> */
    use HasFactory;

    /**
     * @return TFactory
     */
    protected static function newFactory(): Factory
    {
        $module = Str::of(static::class)
            ->after('Modules\\')
            ->before('\\')
            ->value();

        $factory = sprintf(
            'Modules\\%s\\Infrastructure\\Persistence\\Factories\\%sFactory',
            $module,
            class_basename(static::class),
        );

        return $factory::new();
    }
}
