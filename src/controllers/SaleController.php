<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\ViewAction;
use hipanel\modules\client\models\stub\ClientRelationFreeStub;
use hipanel\modules\finance\models\Tariff;
use yii\filters\AccessControl;

class SaleController extends \hipanel\base\CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access-sale' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manage'],
                        'actions' => ['index', 'view'],
                    ],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
                'data' => function ($action) {
                    $sale = $action->model;
                    $attributes = [
                        'id' => $sale->buyer_id,
                        'login' => $sale->buyer,
                        'seller' => $sale->seller,
                        'seller_id' => $sale->seller_id,
                    ];
                    $client = new ClientRelationFreeStub($attributes);
                    $tariff = Tariff::find()->where(['id' => $sale->tariff_id])->one();

                    return compact('client', 'tariff');
                },
            ],
        ]);
    }
}
