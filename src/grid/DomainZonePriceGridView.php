<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\models\DomainZonePrice;
use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\modules\finance\widgets\ResourcePriceWidget;

class DomainZonePriceGridView extends PriceGridView
{
    /**
     * @var DomainZonePrice[][]
     */
    public $parentPrices;

    public function columns()
    {
        return array_merge(parent::columns(), [

            'name' => [
                'label' => '',
                'contentOptions'=> ['style' => 'font-weight:bold'],
                'value' => function($prices) {
                    /** @var DomainZonePrice[] $prices  */
                    return current($prices)->object->name;
                }
            ],
            'registration' => $this->getPriceGrid('domain,dregistration'),
            'transfer' => $this->getPriceGrid('domain,dtransfer'),
            'renewal' => $this->getPriceGrid('domain,drenewal'),
            'deleteInAgp' => $this->getPriceGrid('domain,ddelete_agp'),
            'restoringExpired' => $this->getPriceGrid('domain,drestore_expired'),
            'restoringDeleted' => $this->getPriceGrid('domain,drestore_deleted'),
        ]);
    }

    private function getPriceGrid($type)
    {
        return [
            'label' =>  DomainZonePrice::getTypes()[$type],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'raw',
            'value' => function($prices) use ($type) {
                /** @var DomainZonePrice[] $prices */
                if (!isset($prices[$type])) {
                    return '';
                }
                $price = $prices[$type];
                $parent = $this->parentPrices[$price->object_id][$type] ?? null;
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
