<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers\resource;

use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hiqdev\yii\compat\yii;

class ResourceFilteringService
{
    private ConsumptionConfigurator $configurator;

    public function __construct(?ConsumptionConfigurator $configurator = null)
    {
        $this->configurator = $configurator ?? yii::getContainer()->get(ConsumptionConfigurator::class);
    }

    public function filterByAvailableTypes(array $resources): array
    {
        static $types;
        if ($types === null) {
            $allPossibleColumns = array_flip($this->configurator->getAllPossibleColumns());
            $types = array_filter(
                $resources,
                static fn($resource) => array_key_exists($resource->type, $allPossibleColumns),
            );
        }

        return $types;
    }

    public function getConfigurator(): ConsumptionConfigurator
    {
        return $this->configurator;
    }
}
