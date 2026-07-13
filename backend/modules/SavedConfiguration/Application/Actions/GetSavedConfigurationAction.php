<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Application\Actions;

use Modules\SavedConfiguration\Application\DTO\SavedConfigurationData;
use Modules\SavedConfiguration\Domain\Contracts\SavedConfigurationRepositoryInterface;
use Modules\Users\Domain\Models\User;

final readonly class GetSavedConfigurationAction
{
    public function __construct(
        private SavedConfigurationRepositoryInterface $savedConfiguration,
    ) {}

    public function execute(
        User $user,
        string $savedConfigurationPublicId,
    ): SavedConfigurationData {
        $savedConfiguration = $this->savedConfiguration
            ->findOwnedByPublicId($user, $savedConfigurationPublicId);

        return SavedConfigurationData::fromModel(
            $savedConfiguration->load('product'),
        );
    }
}
