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
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\helpers\PriceChargesEstimator;
use hipanel\modules\finance\helpers\PriceSort;
use hipanel\modules\finance\models\Plan;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\TargetObject;
use hiqdev\hiart\Collection;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\Event;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

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
                    'update-prices' => 'plan.update',
                    'templates' => 'plan.create',
                    'create-prices' => 'plan.create',
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
                        ->withPrices();
                },
                'data' => function (Action $action, array $data) {
                    return array_merge($data, [
                        'grouper' => new PlanInternalsGrouper($data['model']),
                        'parentPrices' => $this->getParentPrices($data['model']['id'])
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

    public function actionCreatePrices(int $plan_id, int $template_plan_id)
    {
        $plan = Plan::findOne(['id' => $template_plan_id]);

        $suggestions = (new Price)->batchQuery('suggest', [
            'object_id' => $plan_id,
            'plan_id' => $plan_id,
            'template_plan_id' => $template_plan_id,
            'type' => $plan->type,
        ]);

        $this->populateWithPrices($plan, $suggestions);
        $parentPrices = $this->getParentPrices($plan_id);

        $targetPlan = Plan::findOne(['id' => $plan_id]);
        [$name, $id] = [$targetPlan->name, $targetPlan->id];
        $grouper = new PlanInternalsGrouper($plan);
        $action = ['@plan/update-prices', 'id' => $id, 'scenario' => 'create'];

        return $this->render($plan->type . '/' . 'createPrices',
            compact('plan', 'grouper', 'parentPrices', 'action', 'plan_id', 'name', 'id'));
    }

    public function actionSuggestPricesModal($id)
    {
        $plan = Plan::findOne(['id' => $id]);
        if ($plan === null) {
            throw new NotFoundHttpException('Not found');
        }
        $this->layout = false;

        return $this->renderAjax('_suggestPricesModal', ['plan' => $plan]);
    }

    /**
     * @param string|null $object_id Object ID or `null`
     * when the desired templates are not related to a specific object
     * @param string $plan_id
     */
    public function actionTemplates($plan_id, $object_id = null)
    {
        $templates = (new Plan())->query('search-templates', [
            'id' => $plan_id,
            'object_id' => $object_id ?? $plan_id,
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
                'first day of +2 month',
            ]);
        } catch (ResponseErrorException $exception) {
            Yii::$app->response->setStatusCode(412, $exception->getMessage());
            return [
                'formula' => $exception->getResponse()->getData()['_error_ops']['formula'] ?? null
            ];
        }
    }

    public function actionUpdatePrices(int $id, string $scenario = 'update')
    {
        $plan = Plan::find()
            ->byId($id)
            ->withPrices()
            ->one();

        $request = Yii::$app->request;
        if ($request->isPost) {
            try {
                $priceClass = $plan->getDesiredPriceClass();
                /** @var Price $price */
                $price = new $priceClass(['scenario' => $scenario]);
                $prices = $request->post($price->formName());
                $collection = new Collection([
                    'model' => $price,
                    'scenario' => $scenario,
                ]);
                $collection->load($prices);
                if ($collection->save() === false) {
                    if ($scenario === 'create') {
                        Yii::$app->session->addFlash('error', Yii::t('hipanel.finance.price', 'Error occurred during creation of prices'));
                    } elseif ($scenario === 'update') {
                        Yii::$app->session->addFlash('error', Yii::t('hipanel.finance.price', 'Error occurred during prices update'));
                    }
                } else {
                    if ($scenario === 'create') {
                        Yii::$app->session->addFlash('success', Yii::t('hipanel.finance.price', 'Prices were successfully created'));
                    } elseif ($scenario === 'update') {
                        Yii::$app->session->addFlash('success', Yii::t('hipanel.finance.price', 'Prices were successfully updated'));
                    }
                }
                return $this->redirect(['@plan/view', 'id' => $id]);
            } catch (\Exception $e) {
                throw new UnprocessableEntityHttpException($e->getMessage(), 0, $e);
            }
        }

        $grouper = new PlanInternalsGrouper($plan);
        $parentPrices = $this->getParentPrices($id);

        return $this->render($plan->type . '/' . 'updatePrices',
            compact('plan', 'grouper', 'parentPrices'));
    }

    /**
     * @param int $plan_id
     * @return array | null
     */
    private function getParentPrices(int $plan_id)
    {
        $parent_id = (new Plan())->query('get-parent-id', [
            'id' => $plan_id,
        ]);
        $parent_id = $parent_id['parent_id'];
        if ($parent_id === null) {
            return null;
        }

        $parent = Plan::find()
            ->byId($parent_id)
            ->withPrices()
            ->one();

        return $parent ? (new PlanInternalsGrouper($parent))->group() : null;
    }

    /**
     * @param Plan $plan
     * @param array $pricesData
     */
    private function populateWithPrices(Plan $plan, $pricesData): void
    {
        $prices = [];
        foreach ($pricesData as $priceData) {
            $object = ArrayHelper::remove($priceData, 'object');
            if (isset($priceData['plan_type']) &&
                $priceData['plan_type'] === 'certificate') {
                $priceData['class'] = 'CertificatePrice';
            }

            /** @var Price $price */
            $price = Price::instantiate($priceData);
            $price->setScenario('create');
            $price->setAttributes($priceData);
            $price->populateRelation('object', new TargetObject($object));
            $price->trigger(Price::EVENT_AFTER_FIND);
            $prices[] = $price;
        }
        $prices = PriceSort::anyPrices()->values($prices, true);

        $plan->populateRelation('prices', $prices);
    }
}
