<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers\resource;

use hipanel\modules\finance\helpers\ConsumptionConfigurator\ConsumptionConfigurator;
use hipanel\modules\finance\models\proxy\Resource;
use hiqdev\yii\compat\yii;

class ResourceFilteringService
{
    private ConsumptionConfigurator $configurator;

    public function __construct(?ConsumptionConfigurator $configurator = null)
    {
        $this->configurator = $configurator ?? yii::getContainer()->get(ConsumptionConfigurator::class);
    }

    /**
     * @param Resource[] $resources
     * @return Resource[]
     */
    public function filterByAvailableTypes(array $resources): array
    {
        static $allPossibleColumns;
        if ($allPossibleColumns === null) {
            $allPossibleColumns = array_flip($this->configurator->getAllPossibleColumns());
        }

        return array_filter(
            $resources,
            static fn($resource) => array_key_exists($resource->type, $allPossibleColumns),
        );
    }

    public function getConfigurator(): ConsumptionConfigurator
    {
        return $this->configurator;
    }
}
