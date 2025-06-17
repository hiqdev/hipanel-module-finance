<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers\ConsumptionConfiguration;

use hipanel\base\Model;
use hipanel\modules\finance\helpers\ConsumptionConfiguration\ConsumptionConfiguratorData;
use hipanel\modules\finance\helpers\YiiObjectHelper;

class ConsumptionConfiguratorDataFactory
{
    public static function create(
        string $label,
        array $columns,
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
