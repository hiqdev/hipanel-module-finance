<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers\resource;

use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\php\billing\product\AggregateInterface;

class ResourceAnalyticsService
{
    private ResourceFilteringService $filteringService;

    public function __construct()
    {
        $this->filteringService = new ResourceFilteringService();
    }

    /**
     * @param array $resources
     * @return array
     */
    public function prepareDetailView(array $resources): array
    {
        $result = [];
        foreach ($this->filteringService->filterByAvailableTypes($resources) as $resource) {
            $decorator = $resource->buildResourceModel()->decorator();
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
            $amount = $this->convertAmount($resource->buildResourceModel()->decorator());
            $qty = bcadd($qty, $amount, 3);
        }

        return str_replace(".000", "", $qty);
    }

    public function calculateTotal(array $resources): array
    {
        $configurator = $this->filteringService->getConfigurator();

        $totals = [];
        foreach ($this->filteringService->filterByAvailableTypes($resources) as $resource) {
            $decorator = $resource->buildResourceModel()->decorator();
            $type = $resource->type;
            $aggregate = $configurator->getAggregate($this->addOveruseToTypeIfNeeded($type));
            $amount = $this->calculateAmount(
                $aggregate,
                (string)($totals[$type]['amount'] ?? 0),
                $decorator,
            );

            $totals[$type] = [
                'amount' => $amount,
                'unit' => $decorator->displayUnit(),
            ];
        }

        return $totals;
    }

    private function calculateAmount(
        AggregateInterface $aggregate,
        string $amount,
        ResourceDecoratorInterface $decorator
    ): string {
        $converted = $this->convertAmount($decorator);

        return $aggregate->isMax()
            ? max($amount, $converted)
            : bcadd($amount, $converted, 3);
    }

    private function addOveruseToTypeIfNeeded(string $type): string
    {
        if (!str_starts_with($type, \hiqdev\billing\registry\product\GType::overuse->name())) {
            return \hiqdev\billing\registry\product\GType::overuse->name() . ',' . $type;
        }

        return $type;
    }
}
