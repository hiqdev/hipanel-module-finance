<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\ColspanColumn;
use hipanel\modules\finance\models\CertificatePrice;

class CertificatePriceGridView extends PriceGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'purchase' => $this->getPriceGrid('certificate,certificate_purchase'),
            'renewal' => $this->getPriceGrid('certificate,certificate_renewal'),
            'certificate' => [
                'value' => function ($prices) {
                    /** @var CertificatePrice[] $prices  */
                    return current($prices)->object->label;
                }
            ],
        ]);
    }

    private function getPriceGrid($type)
    {
        $result = [
            'class' => ColspanColumn::class,
            'headerOptions' => [
                'class' => 'text-center',
            ],
            'columns' => []
        ];
        foreach (CertificatePrice::getPeriods() as $period => $label) {
            $result['columns'][] = [
                'label' => $label,
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
