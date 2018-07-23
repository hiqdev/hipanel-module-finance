<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\ColspanColumn;
use hipanel\modules\finance\models\CertificatePrice;
use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\modules\finance\widgets\ResourcePriceWidget;
use Yii;

class CertificatePriceGridView extends PriceGridView
{
    /**
     * @var CertificatePrice[]
     */
    public $parentPrices;

    public function columns()
    {
        return array_merge(parent::columns(), [
            'purchase' => $this->getPriceGrid('Purchase', 'certificate,certificate_purchase'),
            'renewal' => $this->getPriceGrid('Renewal', 'certificate,certificate_renewal'),
            'certificate' => [
                'label' => Yii::t('hipanel:finance:tariff', 'Name'),
                'value' => function ($prices) {
                    /** @var CertificatePrice[] $prices  */
                    return current($prices)->object->label;
                }
            ],
        ]);
    }

    private function getPriceGrid($name, $type)
    {
        $result = [
            'label' =>  Yii::t('hipanel:finance:tariff', $name),
            'class' => ColspanColumn::class,
            'headerOptions' => [
                'class' => 'text-center',
            ],
            'columns' => []
        ];
        foreach (CertificatePrice::getPeriods() as $period => $label) {
            $result['columns'][] = [
                'label' => Yii::t('hipanel:finance:tariff', $label),
                'contentOptions' => ['class' => 'text-center'],
                'format' => 'raw',
                'value' => function ($prices) use ($type, $period) {
                    /** @var CertificatePrice[] $prices */
                    if (!isset($prices[$type])) {
                        return '';
                    }
                    $price = $prices[$type];
                    $parent = $this->parentPrices[$price->object_id][$type] ?? null;
                    $parent = $parent ? PriceDifferenceWidget::widget([
                        'new' => $price->getPriceForPeriod($period),
                        'old' => $parent->getPriceForPeriod($period),
                    ]) : '';
                    $price = !(!floatval($price->getPriceForPeriod($period)) && !$parent) ?
                        ResourcePriceWidget::widget([
                            'price' => $price->getPriceForPeriod($period),
                            'currency' => $price->currency
                        ]) : '';
                    return "<div class='col-md-6'>$price</div> <div class='col-md-6'>$parent</div>";
                }
            ];
        }
        return $result;
    }
}
