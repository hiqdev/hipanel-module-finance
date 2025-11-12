<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Collection;

use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Data\ConsumptionConfiguratorData;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\ModelRegistry;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Factory\ConsumptionConfiguratorDataFactory;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\TariffPriceTypeSpecification;
use Traversable;

/**
 * Tariff can't be added to Billing Registry, but it is creating a bunch of problems.
 * So, I created this class to fix them.
 */
class ConsumptionConfiguratorDataCollectionTariffDecorator implements ConsumptionConfiguratorDataCollectionInterface
{
    private TariffPriceTypeSpecification $specification;
    private ModelRegistry $modelRegistry;

    public function __construct(private readonly ConsumptionConfiguratorDataCollectionInterface $collection)
    {
        $this->specification = new TariffPriceTypeSpecification();
        $this->modelRegistry = new ModelRegistry();
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->addTariffToConfiguration($this->collection->getIterator()));
    }

    private function addTariffToConfiguration(Traversable $configurations): array
    {
        list ($defaultModel, $defaultResourceModel) = $this->modelRegistry->getDefaultModels();

        $configurations = iterator_to_array($configurations);

        $configurations['tariff'] = ConsumptionConfiguratorDataFactory::create(
            'Tariff resources',
            $this->specification->getPriceTypeCollection(),
            [],
            $defaultModel,
            $defaultResourceModel,
        );

        return $configurations;
    }

    public function findByTariffName(string $tariffName): ?ConsumptionConfiguratorData
    {
        foreach ($this->getIterator() as $key => $configuratorData) {
            if ($key === $tariffName) {
                return $configuratorData;
            }
        }

        return null;
    }
}
