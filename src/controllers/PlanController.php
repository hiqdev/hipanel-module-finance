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
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
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
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully created')
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully updated')
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
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully deleted')
            ],
            'restore' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel.finance.plan', 'Plan was successfully restored')
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
        $request = Yii::$app->request;

        $response = Plan::perform('calculate-charges', [
            'actions' => $request->post('actions'),
            'prices' => $request->post('prices'),
            'times' => [
                'now',
                'first day of +1 month',
                'first day of +2 month',
            ],
        ]);

        $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());

        $result = [];
        foreach ($response as $time => $charges) {
            $chargesByTarget = [];

            foreach ($charges as $charge) {
                $action = $charge['action'];
                $targetId = $action['target']['id'];
                if (empty($targetId)) {
                    Yii::warning('Action does not contain target ID');
                    continue;
                }
                $actionType = $action['type']['name'];

                $price = $charge['price'];
                $priceType = $price['type']['name'];

                $chargesByTarget[$targetId][$actionType][] = [
                    'price' => $moneyFormatter->format(
                        new Money($price['price']['amount'], new Currency($price['price']['currency']))
                    ),
                    'type' => $priceType
                ];
            }

            $result[date('m Y', strtotime($time))] = $chargesByTarget;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}
