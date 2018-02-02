<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\Action;
use hipanel\actions\IndexAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hiqdev\hiart\Query;
use Yii;
use yii\base\Event;
use yii\web\NotFoundHttpException;

class PlanController extends CrudController
{
    public function actions()
    {
        return array_merge(parent::actions(), [
            'create' => [
                'class' => SmartCreateAction::class,
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
            ],
            'index' => [
                'class' => IndexAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
                'on beforePerform' => function (Event $event) {
                    $action = $event->sender;
                    $action->getDataProvider()->query
                        ->joinWith('sales')
                        ->with([
                            'prices' => function (Query $query) {
                                $query
                                    ->addSelect('main_object_id')
                                    ->joinWith('object')
                                    ->limit('ALL');
                            },
                        ]);
                },
                'data' => function (Action $action, array $data) {
                    [$salesByObject, $pricesByMainObject] = $this->groupSalesAndPrices($data['model']);

                    return array_merge($data, [
                        'salesByObject' => $salesByObject,
                        'pricesByMainObject' => $pricesByMainObject,
                    ]);
                },
            ],
            'set-note' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel', 'Note changed'),
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
        ]);
    }

    public function actionCreatePrices($id)
    {
        $plan = Plan::findOne(['id' => $id]);
        if ($plan === null) {
            throw new NotFoundHttpException('Not found');
        }
        $this->layout = false;

        return $this->renderAjax('_createPrices', ['plan' => $plan]);
    }

    /**
     * Groups prices in $plan by main object ID.
     *
     * Creates fakes sales for price objects, that are not actually sold.
     *
     * @param Plan $model
     * @return array
     */
    protected function groupSalesAndPrices(Plan $model)
    {
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
                    if ($price->object_id === (int)$id) {
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
                    'object' => Yii::t('hipanel.finance.price', 'Unknown object name – no direct object prices exist'),
                    'tariff_id' => $model->id,
                    'object_id' => $id,
                    'tariff_type' => $model->type,
                ]);
            }
        }

        return [$salesByObject, $pricesByMainObject];
    }
}
