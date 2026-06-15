<?php

declare(strict_types=1);

namespace Modules\Shared\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class SharedServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerFactoryResolver();
    }

    private function registerFactoryResolver(): void
    {
        Factory::guessFactoryNamesUsing(static function (string $modelName): string {
            if (str_starts_with($modelName, 'Modules\\')) {
                $module = Str::of($modelName)
                    ->after('Modules\\')
                    ->before('\\')
                    ->value();

                return sprintf(
                    'Modules\\%s\\Database\\Factories\\%sFactory',
                    $module,
                    class_basename($module),
                );
            }

            return 'Database\\Factories\\'.class_basename($modelName).'Factory';
        });
    }
}
