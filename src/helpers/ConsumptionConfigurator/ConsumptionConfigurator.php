<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers\ConsumptionConfigurator;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetResource;
use hiqdev\billing\registry\ResourceDecorator\DecoratedInterface;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use yii\db\ActiveRecordInterface;

final class ConsumptionConfigurator
{
    private ?array $columnsWithLabelsGroupedByClass = null;

    public function __construct(
        private readonly BillingRegistryServiceInterface $billingRegistry,
        private readonly ConsumptionConfiguratorDataCollectionInterface $configuratorDataCollection
    )
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

        $fallback = ConsumptionConfiguratorDataFactory::create(
            $class,
            [],
            [],
            $defaultModel,
            $defaultResourceModel,
        );

        return $this->getConfigurations()[$class] ?? $fallback;
    }

    private function getDefaultModels(): array
    {
        return [
            Target::class,
            TargetResource::class,
        ];
    }

    public function getConfigurations(): array
    {
        return iterator_to_array($this->configuratorDataCollection->getIterator());
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

    public function buildResourceModel(ActiveRecordInterface $resource): DecoratedInterface
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

    public function getAggregate(string $type): AggregateInterface
    {
        return $this->billingRegistry->getAggregate($type);
    }
}
