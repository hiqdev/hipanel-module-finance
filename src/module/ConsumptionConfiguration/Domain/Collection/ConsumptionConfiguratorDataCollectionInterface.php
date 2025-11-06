<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Collection;

use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Data\ConsumptionConfiguratorData;
use Traversable;

interface ConsumptionConfiguratorDataCollectionInterface extends \IteratorAggregate
{
    /**
     * @return Traversable<int, ConsumptionConfiguratorData>
     */
    public function getIterator(): Traversable;

    public function findByTariffName(string $tariffName): ?ConsumptionConfiguratorData;
}
