<?php

declare(strict_types=1);

namespace Modules\Shared\Presentation\Filament;

use Filament\Resources\RelationManagers\RelationManager;

final class ProductRelationRegistrar
{
    /** @var list<class-string<RelationManager>> */
    private array $managers = [];

    /** @param class-string<RelationManager> $manager */
    public function register(string $manager): void
    {
        $this->managers[] = $manager;
    }

    /**
     * @return list<class-string<RelationManager>>
     */
    public function all(): array
    {
        return $this->managers;
    }
}
