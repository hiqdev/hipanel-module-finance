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

use hipanel\modules\finance\models\DomainZonePrice;
use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\modules\finance\widgets\ResourcePriceWidget;
use yii\helpers\Html;

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
                'value' => function ($prices) {
                    /** @var DomainZonePrice[] $prices */
                    return current($prices)->object->name;
                },
            ],
            'registration' => $this->getPriceGrid('domain,dregistration'),
            'transfer' => $this->getPriceGrid('domain,dtransfer'),
            'renewal' => $this->getPriceGrid('domain,drenewal'),
            'deleteInAgp' => $this->getPriceGrid('domain,ddelete_agp'),
            'restoringExpired' => $this->getPriceGrid('domain,drestore_expired'),
            'restoringDeleted' => $this->getPriceGrid('domain,drestore_deleted'),
        ]);
    }

    /**
     * @param string $type
     * @return array
     */
    private function getPriceGrid(string $type): array
    {
        return [
            'label' =>  DomainZonePrice::getTypes()[$type],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'raw',
            'value' => function ($prices) use ($type) {
                /** @var DomainZonePrice[] $prices */
                if (!isset($prices[$type])) {
                    return '';
                }
                $price = $prices[$type];
                $parent = $this->parentPrices[$price->object_id][$type] ?? null;

                $moneyPrice =  $price->getMoney();
                $parentValue = '';
                if ($parent) {
                    $parentPrice = $parent->getMoney();
                    $parentValue = PriceDifferenceWidget::widget([
                        'new'         => $moneyPrice,
                        'old'         => $parentPrice,
                    ]);
                }
                $priceValue = floatval($price->price) ||
                (!floatval($price->price) && $parent) ?
                    ResourcePriceWidget::widget([
                        'price' => $moneyPrice,
                    ]) : '';
                $options = ['class' => 'prices-cell'];

                return Html::tag('div', "<div class='left-table-item'>$priceValue</div><div class='right-table-item'>$parentValue</div>", $options);
            },
        ];
    }
}
