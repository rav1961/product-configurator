<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\Actions;

use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\RulesEngine\Application\DTO\RuleEvaluationData;
use Modules\RulesEngine\Domain\Contracts\RuleGraphRepositoryInterface;
use Modules\RulesEngine\Domain\Services\RuleEvaluator;

final readonly class EvaluateRulesAction
{
    public function __construct(
        private RuleGraphRepositoryInterface $graph,
        private RuleEvaluator $evaluator,
    ) {}

    public function execute(
        string $productPublicId,
        ConfigurationSelection $selection,
    ): RuleEvaluationData {
        $rules = $this->graph->buildActiveForProductPublicId($productPublicId);

        return $this->evaluator->evaluate($productPublicId, $selection, $rules);
    }
}
