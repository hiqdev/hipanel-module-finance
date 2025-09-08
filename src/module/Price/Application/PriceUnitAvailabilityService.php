<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Application;

use hipanel\modules\finance\module\Price\Domain\Collection\UnitCollectionInterface;
use hipanel\modules\finance\module\Price\Infrastructure\Persistence\RefUnitRepository;
use hiqdev\billing\registry\Application\UnitService;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\UnitInterface;
use hiqdev\php\billing\product\Exception\PriceTypeDefinitionNotFoundException;

final class PriceUnitAvailabilityService
{
    public function __construct(
        private readonly BillingRegistryServiceInterface $billingRegistryService,
        private readonly RefUnitRepository $unitRepository,
        private readonly UnitService $unitService,
    ) {
    }

    public function getAvailableUnitsForPrice(string $priceTypeName, string $defaultUnitCode): UnitCollectionInterface
    {
        $unitCollection = $this->unitRepository->findAll();

        try {
            $fraction = $this->getPriceTypeDefinitionUnit($priceTypeName)->fractionUnit();

            return $unitCollection->filterByFraction($fraction, $this->unitService);
        } catch (PriceTypeDefinitionNotFoundException) {
            return $unitCollection->getDefaultUnits($defaultUnitCode);
        }
    }

    private function getPriceTypeDefinitionUnit(string $priceTypeName): UnitInterface
    {
        return $this->billingRegistryService
            ->getPriceTypeDefinitionByPriceTypeName($priceTypeName)
            ->getUnit();
    }
}
