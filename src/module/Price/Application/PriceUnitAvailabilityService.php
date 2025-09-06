<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Application;

use hipanel\modules\finance\module\Price\Domain\Collection\UnitCollection;
use hipanel\modules\finance\module\Price\Domain\Collection\UnitCollectionInterface;
use hipanel\modules\finance\module\Price\Infrastructure\Persistence\RefUnitRepository;
use hiqdev\billing\registry\Application\UnitService;
use hiqdev\billing\registry\Domain\Model\Unit\FractionUnit;
use hiqdev\billing\registry\Domain\Model\Unit\Unit as RegistryUnit;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;

final class PriceUnitAvailabilityService
{
    public function __construct(
        private readonly BillingRegistryServiceInterface $billingRegistryService,
        private readonly RefUnitRepository $unitRepository,
        private readonly UnitService $unitCatalog,
    ) {
    }

    public function getAvailableUnitsForPrice(string $priceTypeName, string $defaultUnitCode): UnitCollectionInterface
    {
        $allUnits = $this->unitRepository->findAll();

        try {
            $priceTypeDefinition = $this->billingRegistryService->getPriceTypeDefinitionByPriceTypeName($priceTypeName);
            $fraction = $priceTypeDefinition->getUnit()->fractionUnit();

            return $this->filterUnitsByFraction($allUnits, $fraction);
        } catch (\Exception) {
            return $this->getDefaultUnits($allUnits, $defaultUnitCode);
        }
    }

    private function filterUnitsByFraction(
        UnitCollectionInterface $unitCollection,
        FractionUnit $fraction,
    ): UnitCollectionInterface {
        $fractionUnits = $this->unitCatalog->getUnitsByFraction($fraction);

        // Create a lookup set of allowed unit codes
        $allowedCodes = array_flip(array_map(fn (RegistryUnit $u) => $u->name(), $fractionUnits));

        $filteredUnitCollection = new UnitCollection();
        foreach ($unitCollection as $unit) {
            if (array_key_exists($unit->code, $allowedCodes,)) {
                $filteredUnitCollection->add($unit);
            }
        }

        return $filteredUnitCollection;
    }

    /**
     * Returns fallback default units if PriceType is unknown.
     */
    private function getDefaultUnits(
        UnitCollectionInterface $unitCollection,
        string $defaultUnitCode,
    ): UnitCollectionInterface {
        $defaultCollection = new UnitCollection();

        foreach ($unitCollection as $unit) {
            if ($unit->code === $defaultUnitCode) {
                $defaultCollection->add($unit);
            }
        }

        return $defaultCollection;
    }
}
