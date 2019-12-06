<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\models\DomainServicePrice;
use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\modules\finance\widgets\ResourcePriceWidget;
use yii\helpers\Html;

class DomainServicePriceGridView extends PriceGridView
{
    /**
     * @var DomainServicePrice[]
     */
    public $parentPrices;

    public function columns()
    {
        return array_merge(parent::columns(), [
            'purchase' => $this->getPriceGrid('feature,premium_dns_purchase'),
            'renewal' => $this->getPriceGrid('feature,premium_dns_renew'),
        ]);
    }

    /**
     * @param string $type
     * @return array
     */
    private function getPriceGrid(string $type): array
    {
        return [
            'label' =>  DomainServicePrice::getOperations()[$type],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'raw',
            'value' => function ($prices) use ($type) {
                /** @var DomainServicePrice[] $prices */
                if (!isset($prices[$type])) {
                    return '';
                }
                $price = $prices[$type];
                $parent = $this->parentPrices[$type] ?? null;
                $parentValue = $parent ? PriceDifferenceWidget::widget([
                    'new' => $price->getMoney(),
                    'old' => $parent->getMoney(),
                ]) : '';
                $priceValue = floatval($price->price) ||
                (!floatval($price->price) && $parentValue) ?
                    ResourcePriceWidget::widget([
                        'price' => $price->getMoney(),
                    ]) : '';
                $options = ['class' => 'col-md-6'];

                return Html::tag('div', $priceValue, $options) .
                    Html::tag('div', $parentValue, $options);
            },
        ];
    }
}
