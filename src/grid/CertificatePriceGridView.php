<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\ColspanColumn;
use hipanel\modules\finance\models\CertificatePrice;
use Yii;

class CertificatePriceGridView extends PriceGridView
{
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
                    $price = $prices[$type] ?? null;
                    return $price ? \hipanel\modules\finance\widgets\ResourcePriceWidget::widget([
                        'price' => $price->getPriceForPeriod($period),
                        'currency' => $price->currency
                    ]) : '';
                }
            ];
        }
        return $result;
    }
}
