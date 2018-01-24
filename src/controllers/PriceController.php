<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use Yii;

class PriceController extends CrudController
{
    public function actions()
    {
        return array_merge(parent::actions(), [
            'create' => [
                'class' => SmartCreateAction::class,
                'data' => function ($action, $data) {
                    $plan = null;
                    if ($plan_id = Yii::$app->request->get('plan_id')) {
                        $plan = Plan::findOne(['id' => $plan_id]);
                    }

                    return compact('plan');
                },
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
            ],
            'index' => [
                'class' => IndexAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
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

    public function actionSuggest($object_id, $plan_id, $type = 'default')
    {
        $plan = Plan::findOne(['id' => $plan_id]);

        $suggestions = (new Price)->batchQuery('suggest', [
            'object_id' => $object_id,
            'plan_id' => $plan_id,
            'type' => $type
        ]);

        $models = [];
        foreach ($suggestions as $suggestion) {
            $models[] = new Price([
                'type' => $suggestion['type']['name'],
                'type_id' => $suggestion['type']['id'],
                'object_id' => $suggestion['target']['id'],
                'object' => $suggestion['target']['name'],
                'unit' => $suggestion['prepaid']['unit'],
                'quantity' => $suggestion['prepaid']['quantity'],
                'unit_id' => 0, // todo
                'price' => $suggestion['price']['amount'] / 100,
                'currency' => $suggestion['price']['currency']
            ]);
        }

        return $this->render('create', [
            'model' => reset($models),
            'models' => $models,
            'plan' => $plan
        ]);
    }
}
