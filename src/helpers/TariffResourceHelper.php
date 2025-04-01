<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use hipanel\base\Model;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetResource;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorData;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\billing\registry\ResourceDecorator\server\Traffic95ResourceDecorator;

/**
 * Tariff can't be added to Billing Registry, but it is creating a bunch of problems.
 * So, I created this class to fix them.
 */
class TariffResourceHelper
{
    public function addTariffToConfiguration(array $configurations): array
    {
        list ($defaultModel, $defaultResourceModel) = $this->getDefaultModels();

        $configurations['tariff'] = new ConsumptionConfiguratorData(
            'Tariff resources',
            $this->getTariffColumns(),
            [],
            $this->createObject($defaultModel),
            $this->createObject($defaultResourceModel),
        );

        return $configurations;
    }

    private function createObject(string $className, array $params = []): Model
    {
        return YiiObjectHelper::createObject($className, $params);
    }

    private function getTariffColumns(): array
    {
        return [
            PriceType::server_traf95_max->name(),
            PriceType::server_traf95->name(),
            PriceType::server_traf95_in->name(),
        ];
    }

    private function getDefaultModels(): array
    {
        return [
            Target::class,
            TargetResource::class,
        ];
    }

    public function getResourceDecorator(
        ResourceDecoratorData $resourceData,
        string $priceType
    ): ?ResourceDecoratorInterface {
        if (in_array($priceType, $this->getTariffColumns())) {
            return new Traffic95ResourceDecorator($resourceData);
        }

        return null;
    }
}
