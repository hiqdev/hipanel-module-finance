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

use hipanel\grid\ColspanColumn;
use hipanel\modules\finance\models\CertificatePrice;
use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\modules\finance\widgets\ResourcePriceWidget;
use Yii;
use yii\helpers\Html;

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
                    /** @var CertificatePrice[] $prices */
                    return current($prices)->object->label;
                },
            ],
        ]);
    }

    /**
     * @param string $name column label
     * @param string $type certificate type
     * @return array
     */
    private function getPriceGrid(string $name, string $type): array
    {
        $result = [
            'label' => Yii::t('hipanel:finance:tariff', $name),
            'class' => ColspanColumn::class,
            'headerOptions' => [
                'class' => 'text-center',
            ],
            'columns' => [],
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
                    $parentValue = $parent ? PriceDifferenceWidget::widget([
                        'new' => $price->getPriceForPeriod($period),
                        'old' => $parent->getPriceForPeriod($period),
                    ]) : '';
                    $priceValue = floatval($price->getPriceForPeriod($period)) ||
                    (!floatval($price->getPriceForPeriod($period)) && $parentValue) ?
                        ResourcePriceWidget::widget([
                            'price' => $price->getPriceForPeriod($period),
                            'currency' => $price->currency,
                        ]) : '';
                    $options = ['class' => 'col-md-6'];

                    return Html::tag('div', $priceValue, $options) .
                        Html::tag('div', $parentValue, $options);
                },
            ];
        }

        return $result;
    }
}
