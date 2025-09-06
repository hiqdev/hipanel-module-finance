<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Domain\Collection;

use hipanel\modules\finance\module\Price\Domain\Model\Unit;
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
            $items[$unit->code()] = $unit->label();
        }

        return $items;
    }

    public function isEmpty(): bool
    {
        return $this->units === [];
    }
}
