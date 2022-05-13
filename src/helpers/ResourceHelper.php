<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use hiqdev\yii\compat\yii;

class ResourceHelper
{
    /**
     * @param ResourceDecoratorInterface $decorator
     * @return int|float
     */
    public static function convertAmount(ResourceDecoratorInterface $decorator)
    {
        $configurator = yii::getContainer()->get(ConsumptionConfigurator::class);
        $amount = $decorator->getPrepaidQuantity();
        $targetTypes = $configurator->getAllPossibleColumns();
        unset(
            $targetTypes[array_search('referral', $targetTypes, true)],
            $targetTypes[array_search('ip_num', $targetTypes, true)],
            $targetTypes[array_search('server_files', $targetTypes, true)]
        );
        $convertibleTypes = array_merge([
            'backup_du',
            'cdn_cache',
            'cdn_cache95',
            'cdn_traf',
            'cdn_traf_plain',
            'cdn_traf_ssl',
            'cdn_traf_max',
            'hdd',
            'ram',
            'speed',
            'server_du',
            'server_sata',
            'server_ssd',
            'server_traf95',
            'server_traf95_in',
            'server_traf95_max',
            'server_traf',
            'server_traf_in',
            'server_traf_max',
            'storage_du',
            'storage_du95',
        ], $targetTypes);
        if (in_array($decorator->resource->type, $convertibleTypes, true)) {
            $from = Unit::create($decorator->resource->unit)->getName();
            $to = Unit::create($decorator->toUnit());
            $amount = sprintf('%.3F', Quantity::create($from, $amount)->convert($to)->getQuantity());
        }

        return $amount;
    }

    public static function prepareDetailView(array $resources): array
    {
        $result = [];
        foreach (self::filterByAvailableTypes($resources) as $resource) {
            $decorator = $resource->buildResourceModel()->decorator();
            $result[] = [
                'object_id' => $resource->object_id,
                'date' => $resource->date,
                'type' => $resource->type,
                'type_label' => $decorator->displayTitle(),
                'amount' => self::convertAmount($decorator),
                'unit' => $decorator->displayUnit(),
            ];
        }

        return $result;
    }

    public static function summarize(array $resources): string
    {
        $qty = '0';
        foreach (self::filterByAvailableTypes($resources) as $resource) {
            $decorator = $resource->buildResourceModel()->decorator();
            $amount = self::convertAmount($decorator);
            $qty = bcadd($qty, $amount, 3);
        }

        return $qty;
    }

    public static function calculateTotal(array $resources): array
    {
        $totals = [];
        $totalsOverMax = [
            'cdn_cache',
            'cdn_cache95',
            'cdn_traf95',
            'cdn_traf95_max',
            'server_traf95',
            'server_traf95_in',
            'server_traf95_max',
            'storage_du',
            'storage_du95',
            'server_du',
            'server_sata',
            'server_ssd',
            'server_files',
        ];
        foreach (self::filterByAvailableTypes($resources) as $resource) {
            $decorator = $resource->buildResourceModel()->decorator();
            if (in_array($resource->type, $totalsOverMax, true)) {
                $totals[$resource->type]['amount'] = max(($totals[$resource->type]['amount'] ?? 0), self::convertAmount($decorator));
            } else {
                $totals[$resource->type]['amount'] = bcadd($totals[$resource->type]['amount'] ?? 0, self::convertAmount($decorator), 3);
            }
            $totals[$resource->type]['unit'] = $decorator->displayUnit();
        }

        return $totals;
    }

    public static function filterByAvailableTypes(array $resources): array
    {
        $configurator = yii::getContainer()->get(ConsumptionConfigurator::class);

        return array_filter($resources, static fn($resource) => in_array($resource->type, $configurator->getAllPossibleColumns(), true));
    }
}
