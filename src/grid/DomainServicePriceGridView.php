<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\models\DomainServicePrice;
use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\modules\finance\widgets\ResourcePriceWidget;

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

    private function getPriceGrid($type)
    {
        return [
            'label' =>  DomainServicePrice::getOperations()[$type],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'raw',
            'value' => function($prices) use ($type) {
                /** @var DomainServicePrice[] $prices */
                if (!isset($prices[$type])) {
                    return '';
                }
                $price = $prices[$type];
                $parent = $this->parentPrices[$type] ?? null;
                $parent = $parent ? PriceDifferenceWidget::widget([
                    'new' => $price->price,
                    'old' => $parent->price,
                ]) : '';
                $price = floatval($price->price) || (!floatval($price->price) && $parent) ?
                    ResourcePriceWidget::widget([
                        'price' => $price->price,
                        'currency' => $price->currency
                    ]) : '';
                return "<div class='col-md-6'>$price</div> <div class='col-md-6'>$parent</div>";
            }
        ];
    }
}
