<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\client\models\stub\ClientRelationFreeStub;
use hipanel\modules\finance\actions\ChangeBuyerAction;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\query\SaleQuery;
use Yii;
use yii\base\Event;

class SaleController extends \hipanel\base\CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'create' => 'sale.create',
                    'delete' => 'sale.delete',
                    'update' => 'sale.update',
                    '*' => 'sale.read',
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'on beforePerform' => function (Event $event) {
                    $action = $event->sender;
                    $representation = $action->controller->indexPageUiOptionsModel->representation;
                    if (in_array($representation, ['servers'], true)) {
                        $action->getDataProvider()->query->withServer();
                        $action->getDataProvider()->query->addSelect('tariff_updated_at');
                    }
                },
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
                    $tariff = Plan::find()->where(['id' => $sale->tariff_id])->one();

                    return [
                        'client' => $client,
                        'tariff' => $tariff,
                        ];
                },
            ],
            'create' => [
                'class' => SmartCreateAction::class,
                'success' => Yii::t('hipanel.finance.plan', 'Sale has been successfully created'),
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel.finance.plan', 'Sale has been successfully changed'),
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel:finance:sale', 'Sale was successfully deleted.'),
            ],
            'change-buyer' => [
                'class' => ChangeBuyerAction::class,
                'scenario' => 'change-buyer',
            ],
            'change-buyer-by-one' => [
                'class' => ChangeBuyerAction::class,
                'scenario' => 'change-buyer-by-one',
                'view' => 'modals/change-buyer-by-one',
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
        ]);
    }
}
