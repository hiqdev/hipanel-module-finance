<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Domain\Collection;

use hipanel\modules\finance\module\Price\Domain\Model\Unit;
use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int, Unit>
 */
interface UnitCollectionInterface extends IteratorAggregate
{
    /**
     * @return Traversable<int, Unit>
     */
    public function getIterator(): Traversable;

    public function toArray(): array;

    public function isEmpty(): bool;
}
