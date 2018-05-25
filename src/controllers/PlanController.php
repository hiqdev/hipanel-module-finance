<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\Action;
use hipanel\actions\IndexAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use hipanel\filters\EasyAccessControl;
use hiqdev\hiart\Query;
use Yii;
use yii\base\Event;
use yii\web\NotFoundHttpException;

class PlanController extends CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'create' => 'plan.create',
                    'update' => 'plan.update',
                    '*' => 'plan.read',
                ],
            ],
        ]);
    }

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
                        ->andWhere(['state' => ['ok', 'deleted']])
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
                    return array_merge($data, [
                        'grouper' => new PlanInternalsGrouper($data['model']),
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
            'validate-single-form' => [
                'class' => ValidateFormAction::class,
                'validatedInputId' => false,
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel', 'Deleted'),
            ],
            'restore' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel.finance.plan', 'Restored'),
            ],
            'copy' => [
                'class' => SmartUpdateAction::class,
                'view' => 'modals/copy',
                'queryOptions' => ['batch' => false],
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
}
