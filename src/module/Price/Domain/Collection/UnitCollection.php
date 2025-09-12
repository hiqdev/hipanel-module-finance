<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Domain\Collection;

use hipanel\modules\finance\module\Price\Domain\Model\Unit;
use hiqdev\billing\registry\Application\UnitService;
use hiqdev\php\billing\product\Domain\Model\Unit\FractionUnitInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\UnitInterface;
use Traversable;

final class UnitCollection implements UnitCollectionInterface
{
    /** @var Unit[] $units */
    public function __construct(private array $units = [])
    {
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->units);
    }

    public function add(Unit $unit): void
    {
        $this->units[] = $unit;
    }

    public function toArray(): array
    {
        $items = [];
        foreach ($this->units as $unit) {
            $items[$unit->code] = $unit->label;
        }

        return $items;
    }

    public function isEmpty(): bool
    {
        return $this->units === [];
    }

    public function filterByFraction(
        FractionUnitInterface $fraction,
        UnitService $unitService,
    ): UnitCollectionInterface {
        $fractionUnits = $unitService->getUnitsByFraction($fraction);

        // Create a lookup set of allowed unit codes
        $allowedCodes = array_flip(array_map(fn (UnitInterface $u) => $u->name(), $fractionUnits));

        $filteredUnitCollection = new self();
        foreach ($this->getIterator() as $unit) {
            if (array_key_exists($unit->code, $allowedCodes)) {
                $filteredUnitCollection->add($unit);
            }
        }

        return $filteredUnitCollection;
    }

    /**
     * Returns fallback default units if PriceType is unknown.
     */
    public function getDefaultUnits(
        string $defaultUnitCode,
    ): UnitCollectionInterface {
        $defaultCollection = new self();

        if (!empty($defaultUnitCode)) {
            foreach ($this->getIterator() as $unit) {
                if ($unit->code === $defaultUnitCode) {
                    $defaultCollection->add($unit);
                }
            }
        }

        return $defaultCollection;
    }
}
