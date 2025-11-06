<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Domain;

use hiqdev\billing\registry\Domain\Finance\Enum\PriceType;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorData;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\billing\registry\ResourceDecorator\server\Traffic95ResourceDecorator;
use hiqdev\php\billing\product\Domain\Model\Price\PriceTypeCollection;

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
        if ($this->getPriceTypeCollection()->has($priceType)) {
            return new Traffic95ResourceDecorator($resourceData);
        }

        return null;
    }

    public function getPriceTypeCollection(): PriceTypeCollection
    {
        return new PriceTypeCollection([
            PriceType::server_traf95_max,
            PriceType::server_traf95,
            PriceType::server_traf95_in,
        ]);
    }
}
