<?php

namespace hipanel\modules\finance\helpers;

use yii\helpers\ArrayHelper;
use hipanel\modules\finance\models\proxy\Resource;

class ResourceHelper
{
    /**
     * @param Resource[] $resources
     * @param ResourceConfigurator $configurator
     * @return array
     */
    public static function aggregateByObject(array $resources, ResourceConfigurator $configurator): array
    {
        $result = [];
        foreach ($resources as $resource) {
            if (!in_array($resource->type, $configurator->getRawColumns(), true)) {
                continue;
            }
            $resourceModel = $resource->buildResourceModel($configurator);
            $object = [
                'type' => $resource->type,
            ];
            $object['amount'] = $resourceModel->decorator()->displayAmountWithUnit();
            $result[$resource['object_id']][$resource['type']] = $object;
        }

        return $result;
    }

    /**
     * @param Resource[] $models
     * @return array
     */
    public static function groupResourcesForChart(array $models): array
    {
        $labels = [];
        $data = [];
        ArrayHelper::multisort($models, 'date');
        foreach ($models as $model) {
            $labels[$model->date] = $model;
            $data[$model->type][] = $model->getChartAmount();
        }
        foreach ($labels as $date => $model) {
            $labels[$date] = $model->getDisplayDate();
        }

        return [$labels, $data];
    }
}
