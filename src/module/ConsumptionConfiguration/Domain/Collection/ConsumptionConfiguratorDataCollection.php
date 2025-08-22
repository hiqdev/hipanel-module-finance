<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Collection;

use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\ModelRegistry;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Factory\ConsumptionConfiguratorDataFactory;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Data\ConsumptionConfiguratorData;
use hiqdev\billing\registry\behavior\ConsumptionConfigurationBehavior;
use hiqdev\billing\registry\Domain\Model\TariffType;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use Traversable;

class ConsumptionConfiguratorDataCollection implements ConsumptionConfiguratorDataCollectionInterface
{
    /** @var ConsumptionConfiguratorData[]|null */
    private ?array $configurations = null;

    private ModelRegistry $modelRegistry;

    public function __construct(
        readonly private BillingRegistryServiceInterface $billingRegistry
    ) {
        $this->modelRegistry = new ModelRegistry();
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->getConfigurations());
    }

    private function getConfigurations(): array
    {
        if ($this->configurations === null) {
            $this->configurations = $this->buildConfigurations();
        }

        return $this->configurations;
    }

    /**
     * @return ConsumptionConfiguratorData[]
     */
    private function buildConfigurations(): array
    {
        $configurations = [];
        /** @var ConsumptionConfigurationBehavior $behavior */
        foreach ($this->billingRegistry->getBehaviors(ConsumptionConfigurationBehavior::class) as $behavior) {
            $tariffType = $behavior->getTariffType();

            list ($model, $resourceModel) = $this->getModels($tariffType);

            $configurations[$tariffType->name()] = ConsumptionConfiguratorDataFactory::create(
                $behavior->getLabel(),
                $behavior->getColumns(),
                $behavior->getGroups(),
                $model,
                $resourceModel,
            );
        }

        return $configurations;
    }

    private function getModels(TariffTypeInterface $tariffType): array
    {
        $data = $this->modelRegistry->getDefaultModels();

        if ($tariffType->name() === TariffType::client->name()) {
            $data = [
                \hipanel\modules\client\models\Client::class,
                \hipanel\modules\finance\models\ClientResource::class,
            ];
        } else if ($tariffType->name() === TariffType::server->name()) {
            $data = [
                \hipanel\modules\server\models\Server::class,
                \hipanel\modules\finance\models\ServerResource::class,
            ];
        } else if ($tariffType->name() === TariffType::switch->name()) {
            $data = [
                \hipanel\modules\server\models\Hub::class,
                \hipanel\modules\finance\models\ServerResource::class,
                \hipanel\modules\finance\models\Target::class,
            ];
        }

        return $data;
    }
}
