<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Application;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Data\ConsumptionConfiguratorData;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Collection\ConsumptionConfiguratorDataCollectionInterface;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Factory\ConsumptionConfiguratorDataFactory;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetResource;
use hiqdev\billing\registry\behavior\ConsumptionAggregateBehavior;
use hiqdev\billing\registry\ResourceDecorator\DecoratedInterface;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\Domain\Model\Price\PriceTypeCollection;
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

    public function getColumns(string $class): PriceTypeCollection
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
            new PriceTypeCollection(),
            [],
            $defaultModel,
            $defaultResourceModel,
        );

        return $this->getConfiguratorDataCollection()->findByTariffName($class) ?? $fallback;
    }

    private function getDefaultModels(): array
    {
        return [
            Target::class,
            TargetResource::class,
        ];
    }

    public function getConfiguratorDataCollection(): ConsumptionConfiguratorDataCollectionInterface
    {
        return $this->configuratorDataCollection;
    }

    public function getGroupsWithLabels(string $class): array
    {
        $groups = [];
        $columnsWithLabels = $this->getColumnsWithLabels($class);
        foreach ($this->getGroups($class) as $i => $priceTypeNames) {
            foreach ($priceTypeNames as $priceTypeName) {
                $groups[$i][$priceTypeName] = $columnsWithLabels[$priceTypeName];
            }
        }

        return $groups;
    }

    /**
     * @param string $class
     *
     * @return string[][]
     */
    private function getGroups(string $class): array
    {
        $priceTypeGroups = [];
        $columns = $this->getColumns($class)->names();
        foreach ($this->getConfigurationByClass($class)->groups as $priceTypeCollection) {
            $priceTypeGroups[] = $priceTypeCollection->names();
            foreach ($priceTypeCollection as $priceType) {
                $columns = array_diff($columns, [$priceType->name()]);
            }
        }
        foreach ($columns as $column) {
            $priceTypeGroups[] = [$column];
        }

        return $priceTypeGroups;
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
            foreach ($this->getConfiguratorDataCollection() as $class => $configuration) {
                $columns = $configuration->columns;

                $this->columnsWithLabelsGroupedByClass[$class] = [];
                foreach ($columns as $column) {
                    $decorator = $this->getDecorator($class, $column->name());

                    $this->columnsWithLabelsGroupedByClass[$class][$column->name()] = $decorator->displayTitle();
                }
            }
        }

        return $this->columnsWithLabelsGroupedByClass;
    }

    public function getClassesDropDownOptions(): array
    {
        $configurations = iterator_to_array($this->getConfiguratorDataCollection());

        return array_filter(ArrayHelper::getColumn(
            $configurations,
            static function (ConsumptionConfiguratorData $config): ?string {
                if ($config->hasColumns()) {
                    return $config->getLabel();
                }

                return null;
            }
        ));
    }

    public function getAllPossibleColumns(): array
    {
        $columns = [];
        foreach ($this->getConfiguratorDataCollection() as $configuration) {
            $columns = array_merge($configuration->columns->names(), $columns);
        }

        return array_unique($columns);
    }

    public function getAllPossibleColumnsWithLabels(): array
    {
        $allPossibleColumnsWithLabels = [];
        foreach ($this->getCachedColumnsWithLabelsGroupedByClass() as $columns) {
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
        foreach ($this->getConfiguratorDataCollection() as $key => $configuration) {
            return $key;
        }

        return '';
    }

    /**
     * @return ConsumptionAggregateBehavior|BehaviorInterface
     */
    public function getConsumptionAggregateBehavior(string $type)
    {
        return $this->billingRegistry->getBehavior($type, ConsumptionAggregateBehavior::class);
    }
}
