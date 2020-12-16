<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use Yii;
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
            $result[$resource->object_id][$resource->type][] = $resource->buildResourceModel($configurator);
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

    /**
     * @param ResourceDecoratorInterface $decorator
     * @return int|float
     */
    public static function convertAmount(ResourceDecoratorInterface $decorator)
    {
        $amount = $decorator->getPrepaidQuantity();
        $convertibleTypes = [
            'backup_du',
            'hdd',
            'ram',
            'speed',
            'server_traf95_max',
            'server_traf95_in',
            'server_traf95',
            'server_traf_max',
            'server_traf_in',
            'server_traf',
            'server_du',
        ];
        if (in_array($decorator->resource->type, $convertibleTypes, true)) {
            $amount = Quantity::create(Unit::create($decorator->resource->unit), $amount)
                ->convert(Unit::create($decorator->toUnit()))
                ->getQuantity();
        }

        return $amount;
    }

    public static function prepare(array $resources): array
    {
        $result = [];
        foreach ($resources as $id => $types) {
            foreach ($types as $type => $models) {
                foreach ($models as $resource) {
                    $decorator = $resource->decorator();
                    $result[$id][$type]['qty'] += self::convertAmount($decorator);
                    $result[$id][$type]['unit'] = $decorator->displayUnit();
                }
            }
            self::normalizeQuantity($result[$id]);
        }

        return $result;
    }

    public static function calculateTotal(array $resources, ResourceConfigurator $configurator): array
    {
        $total = [];
        foreach ($resources as $types) {
            foreach ($types as $type => $models) {
                foreach ($models as $resource) {
                    $decorator = $resource->decorator();
                    $total[$type]['qty'] += self::convertAmount($decorator);
                    $total[$type]['unit'] = $decorator->displayUnit();
                }
            }
        }
        $totalGroups = $configurator->getTotalGroups();
        if (empty($totalGroups)) {
            self::normalizeQuantity($total);

            return $total;
        }
        $total = $configurator->modifyTotalGroups($total);
        self::normalizeQuantity($total);

        return $total;
    }

    public static function normalizeQuantity(array &$data): void
    {
        array_walk($data, static function (&$item): void {
            $item['qty'] = number_format($item['qty'], 3);
        });
    }

    public static function getResourceLoader(): string
    {
        Yii::$app->view->registerCss(<<<CSS
.resource-spinner {
  width: 50px;
  height: 10px;
  text-align: center;
  font-size: 10px;
  display: inline-block;
}

.resource-spinner > div {
  background-color: #b8c7ce;
  height: 100%;
  width: 6px;
  display: inline-block;
  margin-right: .1rem;
  
  -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
  animation: sk-stretchdelay 1.2s infinite ease-in-out;
}

.resource-spinner .rect2 {
  -webkit-animation-delay: -1.1s;
  animation-delay: -1.1s;
}

.resource-spinner .rect3 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

.resource-spinner .rect4 {
  -webkit-animation-delay: -0.9s;
  animation-delay: -0.9s;
}

.resource-spinner .rect5 {
  -webkit-animation-delay: -0.8s;
  animation-delay: -0.8s;
}

@-webkit-keyframes sk-stretchdelay {
  0%, 40%, 100% { -webkit-transform: scaleY(0.4) }
  20% { -webkit-transform: scaleY(1.0) }
}

@keyframes sk-stretchdelay {
  0%, 40%, 100% { 
    transform: scaleY(0.4);
    -webkit-transform: scaleY(0.4);
  }  20% { 
    transform: scaleY(1.0);
    -webkit-transform: scaleY(1.0);
  }
}
CSS
            , [], 'resource_spinner_css');

        return '<div class="resource-spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>';
    }
}
