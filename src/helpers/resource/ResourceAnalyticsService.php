<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers\resource;

use hipanel\modules\finance\models\proxy\Resource;
use hipanel\modules\finance\module\ConsumptionConfiguration\Application\ConsumptionConfigurator;
use hiqdev\billing\registry\behavior\Consumption\MaxConsumptionAggregateStrategy;
use hiqdev\billing\registry\behavior\Consumption\SumConsumptionAggregateStrategy;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;

class ResourceAnalyticsService
{
    private ResourceFilteringService $filteringService;

    public function __construct()
    {
        $this->filteringService = new ResourceFilteringService();
    }

    /**
     * @param Resource[] $resources
     * @return array
     */
    public function prepareDetailView(array $resources): array
    {
        $result = [];
        foreach ($this->filteringService->filterByAvailableTypes($resources) as $resource) {
            $decorator = $resource->decorator();
            $result[] = [
                'object_id' => $resource->object_id,
                'date' => $resource->date,
                'type' => $resource->type,
                'type_label' => $decorator->displayTitle(),
                'amount' => $this->convertAmount($decorator),
                'unit' => $decorator->displayUnit(),
            ];
        }

        return $result;
    }

    private function convertAmount(ResourceDecoratorInterface $decorator): string
    {
        return (string)\hiqdev\billing\registry\helper\ResourceHelper::convertAmount($decorator);
    }

    public function summarize(array $resources): string
    {
        $qty = '0';
        foreach ($this->filteringService->filterByAvailableTypes($resources) as $resource) {
            $amount = $this->convertAmount($resource->decorator());
            $qty = (new SumConsumptionAggregateStrategy())->aggregate($qty, $amount);
        }

        return str_replace(".000", "", $qty);
    }

    public function calculateTotal(array $resources): array
    {
        $configurator = $this->filteringService->getConfigurator();

        $totals = [];
        foreach ($this->filteringService->filterByAvailableTypes($resources) as $resource) {
            $decorator = $resource->decorator();
            $type = $resource->type;

            $amount = $this->calculateAmount(
                $configurator,
                $type,
                (string)($totals[$type]['amount'] ?? 0),
                $this->convertAmount($decorator),
            );

            $totals[$type] = [
                'amount' => $amount,
                'unit' => $decorator->displayUnit(),
            ];
        }

        return $totals;
    }

    private function calculateAmount(
        ConsumptionConfigurator $configurator,
        string $type,
        string $amount1,
        string $amount2,
    ): string {
        try {
            $aggregateBehavior = $configurator->getConsumptionAggregateBehavior($this->addOveruseToTypeIfNeeded($type));
        } catch (BehaviorNotFoundException) {
            return (new MaxConsumptionAggregateStrategy())->aggregate($amount1, $amount2);
        }

        return $aggregateBehavior->aggregate($amount1, $amount2);
    }

    private function addOveruseToTypeIfNeeded(string $type): string
    {
        if (!str_starts_with($type, \hiqdev\billing\registry\product\GType::overuse->name())) {
            return \hiqdev\billing\registry\product\GType::overuse->name() . ',' . $type;
        }

        return $type;
    }
}
