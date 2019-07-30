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

                $parentValue = '';
                if ($parent) {
                    $parentValue = ResourcePriceWidget::widget([
                        'price' => $parent->price,
                        'currency' => $parent->currency,
                    ]);
                    if ($parent->currency === $price->currency) {
                        $parentValue = PriceDifferenceWidget::widget([
                            'new' => $price->price,
                            'old' => $parent->price,
                        ]);
                    }
                }
                $priceValue = floatval($price->price) || (!floatval($price->price) && $parent) ?
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
