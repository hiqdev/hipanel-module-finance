<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use Yii;

/**
 * Class PlanInternalsGrouper can be used to group prices inside $plan depending on
 * different factors.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PlanInternalsGrouper
{
    /**
     * @var Plan
     */
    private $plan;

    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * Should be used to group prices of [[Plan]] with type `server`.
     *
     * @return array of two elements:
     * 0: sales, grouped by sold object
     * 1: prices, grouped by sold object
     */
    public function groupServerPrices()
    {
        $model = $this->plan;
        /** @var Sale[] $salesByObject */
        $salesByObject = [];
        /** @var Price[][] $pricesByMainObject */
        $pricesByMainObject = [];

        foreach ($model->prices as $price) {
            $pricesByMainObject[$price->main_object_id ?? $model->id][$price->id] = $price;
        }

        if (isset($pricesByMainObject[null])) {
            $salesByObject[null] = new FakeSale([
                'object' => Yii::t('hipanel.finance.price', 'Applicable for all objects'),
                'tariff_id' => $model->id,
            ]);
        }
        if (isset($pricesByMainObject[$model->id])) {
            $salesByObject[$model->id] = new FakeSale([
                'object' => Yii::t('hipanel.finance.price', 'For the whole tariff'),
                'tariff_id' => $model->id,
                'object_id' => $model->id,
            ]);
        }
        foreach ($model->sales as $sale) {
            $salesByObject[$sale->object_id] = $sale;
        }

        foreach ($pricesByMainObject as $id => $prices) {
            if (!isset($salesByObject[$id])) {
                foreach ($prices as $price) {
                    if ((int)$price->object_id === (int)$id) {
                        $salesByObject[$id] = new FakeSale([
                            'object' => $price->object->name,
                            'tariff_id' => $model->id,
                            'object_id' => $price->object_id,
                            'tariff_type' => $model->type,
                        ]);
                        continue 2;
                    }
                }

                $salesByObject[$id] = new FakeSale([
                    'object' => Yii::t('hipanel.finance.price', 'Unknown object name - no direct object prices exist'),
                    'tariff_id' => $model->id,
                    'object_id' => $id,
                    'tariff_type' => $model->type,
                ]);
            }
        }

        return [$salesByObject, $pricesByMainObject];
    }
}
