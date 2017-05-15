<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\ViewAction;
use hipanel\modules\client\models\stub\ClientRelationFreeStub;
use hipanel\modules\finance\logic\TariffManagerFactory;
use hipanel\modules\finance\models\Tariff;
use yii\base\Event;

class SaleController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
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
        ];
    }
}
