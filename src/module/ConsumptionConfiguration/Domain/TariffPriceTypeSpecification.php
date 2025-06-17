<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Domain;

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
class TariffPriceTypeSpecification
{
    public function getResourceDecorator(
        ResourceDecoratorData $resourceData,
        string $priceType
    ): ?ResourceDecoratorInterface {
        if (in_array($priceType, $this->getTariffColumns())) {
            return new Traffic95ResourceDecorator($resourceData);
        }

        return null;
    }

    public function getTariffColumns(): array
    {
        return [
            PriceType::server_traf95_max->name(),
            PriceType::server_traf95->name(),
            PriceType::server_traf95_in->name(),
        ];
    }
}
