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
use hipanel\actions\RedirectAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\actions\PriceUpdateAction;
use hipanel\modules\finance\collections\PricesCollection;
use hipanel\modules\finance\helpers\PriceSort;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\query\PriceQuery;
use hipanel\modules\finance\models\TargetObject;
use Yii;
use yii\base\DynamicModel;
use yii\base\Event;
use function Webmozart\Assert\Tests\StaticAnalysis\string;

/**
 * Class PriceController.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceController extends CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'create' => 'price.create',
                    'update' => 'price.update',
                    'delete' => 'price.delete',
                    'create-suggested' => 'price.update',
                    '*' => ['plan.read', 'price.read'],
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
                    /** @var PriceQuery $query */
                    $query = $event->sender->getDataProvider()->query;
                    $query
                        ->withMainObject()
                        ->withPlan()
                        ->withFormulaLines();
                },
            ],
            'view' => [
                'class' => ViewAction::class,
            ],
            'create' => [
                'class' => SmartCreateAction::class,
                'data' => function ($action, $data) {
                    $plan = null;
                    if ($plan_id = Yii::$app->request->get('plan_id')) {
                        $plan = Plan::findOne(['id' => $plan_id]);
                    }

                    return compact('plan');
                },
                'success' => Yii::t('hipanel.finance.price', 'Prices were successfully created'),
            ],
            'create-suggested' => [
                'class' => SmartCreateAction::class,
                'collection' => ['class' => PricesCollection::class],
                'scenario' => 'create',
                'POST' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function (RedirectAction $action) {
                            return ['@plan/view', 'id' => $action->collection->getModel()->plan_id];
                        },
                    ],
                ],
                'success' => Yii::t('hipanel.finance.price', 'Prices were successfully created'),
            ],
            'update' => [
                'class' => PriceUpdateAction::class,
                'collection' => ['class' => PricesCollection::class],
                'success' => Yii::t('hipanel.finance.price', 'Prices were successfully updated'),
                'scenario' => 'update',
                'on beforeSave' => function (Event $event) {
                    /** @var \hipanel\actions\Action $action */
                    $action = $event->sender;
                    $action->collection->load();
                },
                'on beforeFetch' => function (Event $event) {
                    /** @var PriceQuery $query */
                    $query = $event->sender->getDataProvider()->query;
                    $query
                        ->withFormulaLines()
                        ->withMainObject();
                },
                'data' => function ($action, $data) {
                    $data['models'] = PriceSort::anyPrices()->values($data['models'], true);

                    return $data;
                },
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel.finance.price', 'Prices were successfully deleted'),
            ],
            'set-note' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel', 'Note changed'),
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
                'collection' => ['class' => PricesCollection::class],
            ],
        ]);
    }

    public function actionAddExtraPrices(int $plan_id, string $type)
    {
        $plan = Plan::findOne(['id' => $plan_id]);
        $typeMap = [
            'calculator_public_cloud' => 'config',
            'calculator_private_cloud' => 'model',
        ];
        $model = new DynamicModel(['id', 'price_type' => 'monthly,hardware', 'type' => $typeMap[$type], 'name']);
        $models = [];
        $selection = $this->request->post('selection', []);
        $newObjects = $this->request->post($model->formName(), []);
        if (!empty($selection)) {
            foreach ($selection as $item) {
                $clone = clone $model;
                $clone->id = $item['object_id'];
                $clone->name = $item['name'];
                $clone->type = $typeMap[$type];
                $clone->price_type = $item['price_type'];
                $models[] = $clone;
            }
        }
        if (!empty($newObjects)) {
            $suggestions = [];
            foreach ($newObjects as $object) {
                $object_id = $object['id'];
                $object['type'] = empty($object['type']) ? $typeMap[$type] : $object['type'];
                $newObject = [
                    'id' => $object_id,
                    'type' => $object['type'],
                    'name' => $object['name'],
                ];
                $suggestions[] = $this->createPrice([
                    'type' => 'monthly,hardware',
                    'main_object_id' => $object_id,
                    'main_object_name' => $object['name'],
                    'object_id' => $object_id,
                    'object' => $newObject,
                    'plan_id' => $plan_id,
                    'unit' => 'items',
                    'quantity' => 0,
                    'price' => $selection[$object_id]['price'] ?? 0,
                    'currency' => 'USD',
                    'note' => $selection[$object_id]['note'] ?? '',
                    'subprices' => [
                        'EUR' => [
                            'amount' => $selection[$object_id]['eur'] ?? 0,
                            'currency' => 'EUR',
                        ],
                    ],
                ], $newObject);
                if ($type === 'calculator_public_cloud') {
                    $trafficExist = array_filter(
                        $selection,
                        static fn($entry) => (string)$entry['object_id'] === (string)$object['object_id'] && $entry['price_type'] === 'overuse,server_traf_max'
                    );
                    if (!empty($trafficExist)) {
                        $object = reset($trafficExist);
                    }
                    if (preg_match('/(\d+)\s(TB)$/i', $newObject['name'], $matches) !== 0) {
                        $quantity = $matches[1];
                    }
                    $suggestions[] = $this->createPrice([
                        'type' => 'overuse,server_traf_max',
                        'main_object_id' => $object_id,
                        'main_object_name' => $object['name'],
                        'object_id' => $object_id,
                        'object' => [
                            'id' => $object_id,
                            'type' => $object['price_type'],
                            'name' => $object['name'],
                        ],
                        'plan_id' => $plan_id,
                        'unit' => 'tb',
                        'quantity' => $quantity ?? 0,
                        'price' => $selection[$object_id]['price'] ?? 0,
                        'currency' => 'USD',
                        'note' => $selection[$object_id]['note'] ?? '',
                        'subprices' => [
                            'EUR' => [
                                'amount' => $selection[$object_id]['eur'] ?? 0,
                                'currency' => 'EUR',
                            ],
                        ],
                    ], $newObject);
                    unset($quantity);
                }
            }
            $prices = $this->getSuggested($plan_id, $plan_id, null, $type);
            $existingObjects = array_keys(ArrayHelper::map($prices, 'object_id', 'id'));
            $uniqSuggestions = array_filter($suggestions, static fn($suggestion) => !in_array($suggestion->object_id, $existingObjects, true));
            $prices = array_merge($prices, $uniqSuggestions);
            $prices = PriceSort::anyPrices()->values($prices, true);

            return $this->renderAjax('_form', [
                'type' => $type,
                'model' => reset($prices),
                'models' => $prices,
                'plan' => $plan,
            ]);
        }
        if (empty($models)) {
            $models = [$model];
        }
        $models = array_values(array_filter($models, static fn($entry) => $entry->price_type === 'monthly,hardware'));

        return $this->renderAjax('_add-extra-prices', [
            'plan' => $plan,
            'model' => $model,
            'models' => $models,
            'type' => $type,
        ]);
    }

    public function actionSuggest($plan_id, $object_id = null, $template_plan_id = null, string $type = 'default')
    {
        $plan = Plan::findOne(['id' => $plan_id]);
        $models = $this->getSuggested($plan_id, $object_id, $template_plan_id, $type);

        return $this->render('suggested', [
            'type' => $type,
            'model' => reset($models),
            'models' => $models,
            'plan' => $plan,
        ]);
    }

    private function getSuggested($plan_id, $object_id = null, $template_plan_id = null, string $type = 'default'): array
    {
        $suggestions = (new Price())->batchQuery('suggest', [
            'plan_id' => $plan_id,
            'object_id' => $object_id,
            'template_plan_id' => $template_plan_id,
            'type' => $type,
        ]);
        $models = [];
        foreach ($suggestions as $suggestion) {
            $object = ArrayHelper::remove($suggestion, 'object');
            $models[] = $this->createPrice($suggestion, $object);
        }
        $models = PriceSort::anyPrices()->values($models, true);

        return $models;
    }

    private function createPrice(array $suggestion, array $object): Price
    {
        /** @var Price $price */
        unset($suggestion['id']);
        $price = Price::instantiate($suggestion);
        $price->setScenario('create');
        $price->setAttributes($suggestion);
        $price->populateRelation('object', new TargetObject($object));

        return $price;
    }
}
