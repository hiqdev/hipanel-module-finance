<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers\ConsumptionConfiguration;

use Traversable;

/**
 * Tariff can't be added to Billing Registry, but it is creating a bunch of problems.
 * So, I created this class to fix them.
 */
class ConsumptionConfiguratorDataCollectionTariffDecorator implements ConsumptionConfiguratorDataCollectionInterface
{
    private TariffResourceHelper $helper;

    public function __construct(private readonly ConsumptionConfiguratorDataCollectionInterface $collection)
    {
        $this->helper = new TariffResourceHelper();
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->addTariffToConfiguration($this->collection->getIterator()));
    }

    private function addTariffToConfiguration(Traversable $configurations): array
    {
        list ($defaultModel, $defaultResourceModel) = $this->helper->getDefaultModels();

        $configurations = iterator_to_array($configurations);

        $configurations['tariff'] = ConsumptionConfiguratorDataFactory::create(
            'Tariff resources',
            $this->helper->getTariffColumns(),
            [],
            $defaultModel,
            $defaultResourceModel,
        );

        return $configurations;
    }
}
