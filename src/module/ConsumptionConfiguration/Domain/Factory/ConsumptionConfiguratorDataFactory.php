<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Factory;

use hipanel\base\Model;
use hipanel\modules\finance\helpers\YiiObjectHelper;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Data\ConsumptionConfiguratorData;
use hiqdev\php\billing\product\Domain\Model\Price\PriceTypeCollection;

class ConsumptionConfiguratorDataFactory
{
    /**
     * @param PriceTypeCollection[] $groups
     */
    public static function create(
        string $label,
        PriceTypeCollection $columns,
        array $groups,
        string $model,
        string $resourceModel
    ): ConsumptionConfiguratorData {
        return new ConsumptionConfiguratorData(
            $label,
            $columns,
            $groups,
            self::createObject($model),
            self::createObject($resourceModel),
        );
    }

    private static function createObject(string $className): Model
    {
        return YiiObjectHelper::createObject($className);
    }
}
