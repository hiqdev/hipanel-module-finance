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
use hipanel\modules\finance\helpers\PriceChargesEstimator;
use hipanel\modules\finance\models\Plan;
use hipanel\filters\EasyAccessControl;
use hiqdev\hiart\Query;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\Event;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
                    'templates' => 'plan.create',
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
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully created'),
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully updated'),
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
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully deleted'),
            ],
            'restore' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully restored'),
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

    /**
     * @param string $object_id
     * @param string $plan_id
     */
    public function actionTemplates($plan_id, $object_id)
    {
        $templates = (new Plan())->query('search-templates', [
            'id' => $plan_id,
            'object_id' => $object_id,
        ]);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $templates;
    }

    public function actionCalculateCharges()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        /** @var PriceChargesEstimator $calculator */
        $calculator = Yii::$container->get(PriceChargesEstimator::class, [
            $request->post('actions'),
            $request->post('prices'),
        ]);

        try {
            return $calculator->calculateForPeriods([
                'now',
                'first day of +1 month',
                'first day of +1 year',
            ]);
        } catch (ResponseErrorException $exception) {
            Yii::$app->response->setStatusCode(412, $exception->getMessage());
            return [
                'formula' => $exception->getResponse()->getData()['_error_ops']['formula'] ?? null
            ];
        }
    }
}
