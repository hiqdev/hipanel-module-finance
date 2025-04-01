<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use hipanel\base\Model;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetResource;
use hiqdev\billing\registry\behavior\ConsumptionConfigurationBehaviour;
use hiqdev\billing\registry\Domain\Model\TariffType;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\php\billing\product\BillingRegistryInterface;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use yii\db\ActiveRecordInterface;
use Yii;

final class ConsumptionConfigurator
{
    /** @var ConsumptionConfiguratorData[]|null */
    private ?array $configurations = null;

    private ?array $columnsWithLabelsGroupedByClass = null;

    public function __construct(private readonly BillingRegistryInterface $billingRegistry)
    {
    }

    public function getColumns(string $class): array
    {
        return $this->getConfigurationByClass($class)->columns;
    }

    /**
     * @param string $class - for example: load_balancer
     */
    public function getConfigurationByClass(string $class): ConsumptionConfiguratorData
    {
        list ($defaultModel, $defaultResourceModel) = $this->getDefaultModels();

        $fallback = new ConsumptionConfiguratorData(
            $class,
            [],
            [],
            $this->createObject($defaultModel),
            $this->createObject($defaultResourceModel),
        );

        return $this->getConfigurations()[$class] ?? $fallback;
    }

    private function getDefaultModels(): array
    {
        return [
            Target::class,
            TargetResource::class
        ];
    }

    private function createObject(string $className, array $params = []): Model
    {
        return Yii::createObject(array_merge(['class' => $className], $params));
    }

    public function getConfigurations(): array
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
        /** @var ConsumptionConfigurationBehaviour $behavior */
        foreach ($this->billingRegistry->getBehaviors(ConsumptionConfigurationBehaviour::class) as $behavior) {
            $tariffType = $behavior->getTariffType();

            list ($model, $resourceModel) = $this->getModels($tariffType);

            $configurations[$tariffType->name()] = new ConsumptionConfiguratorData(
                $behavior->getLabel(),
                $behavior->getColumns(),
                $behavior->getGroups(),
                $this->createObject($model),
                $this->createObject($resourceModel),
            );
        }

        // Can't be added to Billing Registry, so left as it is
        list ($defaultModel, $defaultResourceModel) = $this->getDefaultModels();
        $configurations['tariff'] = new ConsumptionConfiguratorData(
            'Tariff resources',
            [
                PriceType::server_traf95_max->name(),
                PriceType::server_traf95->name(),
                PriceType::server_traf95_in->name(),
            ],
            [],
            $this->createObject($defaultModel),
            $this->createObject($defaultResourceModel),
        );

        return $configurations;
    }

    private function getModels(TariffTypeInterface $tariffType): array
    {
        $data = $this->getDefaultModels();

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
            ];
        }

        return $data;
    }

    public function getGroupsWithLabels(string $class): array
    {
        $groups = [];
        $columnsWithLabels = $this->getColumnsWithLabels($class);
        foreach ($this->getGroups($class) as $i => $group) {
            foreach ($group as $j => $type) {
                $groups[$i][$type] = $columnsWithLabels[$type];
            }
        }

        return $groups;
    }

    private function getGroups(string $class): array
    {
        $groups = [];
        $columns = $this->getColumns($class);
        foreach ($this->getConfigurationByClass($class)->groups as $group) {
            $groups[] = $group;
            foreach ($group as $item) {
                $columns = array_diff($columns, [$item]);
            }
        }
        foreach ($columns as $column) {
            $groups[] = [$column];
        }

        return $groups;
    }

    public function getColumnsWithLabels(string $searchClass): array
    {
        $result = [];
        foreach ($this->getCachedColumnsWithLabelsGroupedByClass() as $class => $columns) {
            if ($class === $searchClass) {
                foreach ($columns as $column => $label) {
                    $result[$column] = $label;
                }
            }
        }

        return $result;
    }

    /**
     * Please use this method to avoid call heavy getDecorator() method only for retrieve title
     *
     * @return array
     */
    private function getCachedColumnsWithLabelsGroupedByClass(): array
    {
        if ($this->columnsWithLabelsGroupedByClass === null) {
            foreach ($this->getConfigurations() as $class => $configuration) {
                $columns = $configuration->columns;

                $this->columnsWithLabelsGroupedByClass[$class] = [];
                foreach ($columns as $column) {
                    $decorator = $this->getDecorator($class, $column);

                    $this->columnsWithLabelsGroupedByClass[$class][$column] = $decorator->displayTitle();
                }
            }
        }

        return $this->columnsWithLabelsGroupedByClass;
    }

    public function getClassesDropDownOptions(): array
    {
        return array_filter(ArrayHelper::getColumn(
            $this->getConfigurations(),
            static function (ConsumptionConfiguratorData $config): ?string {
                if (!empty($config->columns)) {
                    return $config->getLabel();
                }

                return null;
            }
        ));
    }

    public function getAllPossibleColumns(): array
    {
        $columns = [];
        foreach ($this->getConfigurations() as $configuration) {
            $columns = array_merge($configuration->columns, $columns);
        }

        return array_unique($columns);
    }

    public function getAllPossibleColumnsWithLabels(): array
    {
        $allPossibleColumnsWithLabels = [];
        foreach ($this->getCachedColumnsWithLabelsGroupedByClass() as $class => $columns) {
            foreach ($columns as $column => $label) {
                $allPossibleColumnsWithLabels[$column] = $label;
            }
        }

        return $allPossibleColumnsWithLabels;
    }

    private function getDecorator(string $class, string $type): ResourceDecoratorInterface
    {
        $config = $this->getConfigurationByClass($class);

        $config->resourceModel->type = $type;

        /** @var ResourceDecoratorInterface $decorator */
        $decorator = $config->resourceModel->decorator();

        return $decorator;
    }

    public function buildResourceModel(ActiveRecordInterface $resource): object
    {
        $config = $this->getConfigurationByClass($resource->class);

        $config->resourceModel->setAttributes([
            'type' => $resource->type,
            'unit' => $resource->unit,
            'quantity' => $resource->getAmount(),
        ]);

        return $config->resourceModel;
    }

    public function fillTheOriginalModel(Consumption $consumption): object
    {
        $configuration = $this->getConfigurationByClass($consumption->class);

        $configuration->model->setAttributes($consumption->mainObject, false);

        return $configuration->model;
    }

    public function getFirstAvailableClass(): string
    {
        $configurations = $this->getConfigurations();

        return array_key_first($configurations);
    }
}
