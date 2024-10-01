<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\Action;
use hipanel\actions\IndexAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\VariantsAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\models\Ref;
use hipanel\modules\client\controllers\ContactController;
use hipanel\modules\finance\actions\BillImportFromFileAction;
use hipanel\modules\finance\actions\BillManagementAction;
use hipanel\modules\finance\actions\CreateFromPricesAction;
use hipanel\modules\finance\actions\GenerateInvoiceAction;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillImportForm;
use hipanel\modules\finance\forms\CurrencyExchangeForm;
use hipanel\modules\finance\helpers\ChargesGrouper;
use hipanel\modules\finance\models\ExchangeRate;
use hipanel\modules\finance\models\query\ChargeQuery;
use hipanel\modules\finance\models\Resource;
use hipanel\modules\finance\providers\BillTypesProvider;
use hipanel\modules\finance\widgets\FinanceSummaryTable;
use hipanel\widgets\SynchronousCountEnabler;
use hiqdev\hiart\ActiveDataProvider;
use hiqdev\hiart\Collection;
use Tuck\Sort\Sort;
use Yii;
use yii\base\Event;
use yii\base\Module;
use yii\caching\CacheInterface;
use yii\grid\GridView;
use yii\web\Response;
use yii\web\User;

/**
 *
 * @property-read array $typesAndGroups
 * @property-read array $paymentTypes
 */
class BillController extends CrudController
{
    private BillTypesProvider $billTypesProvider;
    private CacheInterface $cache;
    private User $user;

    public function __construct($id, Module $module, BillTypesProvider $billTypesProvider, CacheInterface $cache, User $user, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->billTypesProvider = $billTypesProvider;
        $this->cache = $cache;
        $this->user = $user;
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access-bill' => [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'create,copy,copy-with-zero-sums,create-from-prices' => 'bill.create',
                    'create-transfer'                                    => 'bill.create',
                    'import'                                             => 'bill.import',
                    'import-from-file'                                   => 'bill.import',
                    'delete'                                             => 'bill.delete',
                    '*'                                                  => 'bill.read',
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'data' => function () {
                    $billTypesList = $this->billTypesProvider->getTypes();
                    $rates = $this->getExchangeRates();

                    return [
                        'rates' => $rates ?? [],
                        'billTypesList' => $billTypesList ?? [],
                        'clientTypes' => $this->getRefs('type,client', 'hipanel:client'),
                    ];
                },
                'responseVariants' => [
                    IndexAction::VARIANT_SUMMARY_RESPONSE => function (VariantsAction $action): string {
                        /** @var ActiveDataProvider $dataProvider */
                        $dataProvider = $action->parent->getDataProvider();
                        $altQuery = clone $dataProvider->query;
                        $lastModifiedBill = $altQuery->where(['groupby' => 'last_modified'])->one();
                        $key = serialize(array_merge($action->controller->request->get(null, []), [$this->user->getId(), $lastModifiedBill->ts]));

                        return $this->cache->getOrSet($key, function () use ($action, $dataProvider): string {
                            $defaultSummary = (new SynchronousCountEnabler($dataProvider, fn(GridView $grid): string => $grid->renderSummary()))();
                            $financeSummary = FinanceSummaryTable::widget([
                                'currencies' => $action->controller->getCurrencyTypes(),
                                'allModels' => $dataProvider->query->andWhere(['groupby' => 'sum_by_currency'])->all(),
                            ]);

                            return $defaultSummary . $financeSummary;
                        }, 3600);
                    },
                ],
            ],
            'view' => [
                'class' => ViewAction::class,
                'on beforePerform' => function (Event $event) {
                    /** @var \hipanel\actions\SearchAction $action */
                    $action = $event->sender;
                    $dataProvider = $action->getDataProvider();
                    $dataProvider->query->joinWith(['charges' => function (ChargeQuery $query) {
                        $query->withCommonObject();
                        $query->withLatestCommonObject();
                    }])->andWhere(['with_charges' => true]);
                },
                'data' => function (Action $action, array $data) {
                    return array_merge($data, [
                        'grouper' => new ChargesGrouper($data['model']->charges),
                        'currencies' => $action->controller->getCurrencyTypes(),
                    ]);
                },
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'validate-bill-form' => [
                'class' => ValidateFormAction::class,
                'collection' => [
                    'class' => Collection::class,
                    'model' => new BillForm(),
                ],
            ],
            'create' => [
                'class' => BillManagementAction::class,
            ],
            'create-from-prices' => [
                'class' => CreateFromPricesAction::class,
            ],
            'generate-invoice' => [
                'class' => GenerateInvoiceAction::class,
            ],
            'import-from-file' => [
                'class' => BillImportFromFileAction::class,
            ],
            'update' => [
                'class' => BillManagementAction::class,
            ],
            'copy' => [
                'class' => BillManagementAction::class,
                'view' => 'create',
                'scenario' => 'create',
                'forceNewRecord' => true,
            ],
            'copy-with-zero-sums' => [
                'class' => BillManagementAction::class,
                'view' => 'create',
                'scenario' => 'create',
                'forceNewRecord' => true,
                'withZeroSums' => true,
            ],
            'create-transfer' => [
                'class' => SmartCreateAction::class,
                'success' => Yii::t('hipanel:finance', 'Transfer was completed'),
                'POST html' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function ($action) {
                            return ['@bill/index'];
                        },
                    ],
                ],
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel:finance', 'Payment was deleted successfully'),
            ],
            'charge-delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel:finance', 'Charge was deleted successfully'),
                'collection' => [
                    'class'     => Collection::class,
                    'model'     => new Resource(['scenario' => 'delete']),
                    'scenario'  => 'delete',
                ],
            ],
            'requisites' => [
                'class' => RedirectAction::class,
                'url' => function ($action) {
                    $identity = Yii::$app->user->identity;
                    $seller = $identity->type === $identity::TYPE_RESELLER ? $identity->username : $identity->seller;
                    if ($seller === 'bullet') {
                        $seller = 'dsr';
                    }

                    return array_merge(ContactController::getSearchUrl(['client' => $seller]), ['representation' => 'requisites']);
                },
            ],
        ]);
    }

    public function actionImport()
    {
        $billTypes = $this->billTypesProvider->getTypes();
        $model = new BillImportForm([
            // Kick out items that are categories names, but not real types
            'billTypes' => array_filter($billTypes, static fn(Ref $type) => str_contains($type->name, ',')),
        ]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $models = $model->parse();

            if ($models !== null) {
                $models = BillForm::createMultipleFromBills($models, 'create');

                return $this->render('create', [
                    'models' => $models,
                    'model' => reset($models),
                    'billTypesList' => $billTypes,
                    'allowedTypes' => [],
                ]);
            }
        }

        return $this->render('import', ['model' => $model]);
    }

    public function actionCreateExchange()
    {
        $model = new CurrencyExchangeForm();
        $canSupport = Yii::$app->user->can('support');
        if (!$canSupport) {
            $model->client_id = Yii::$app->user->identity->getId();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                Yii::$app->session->addFlash('success', Yii::t('hipanel:finance', 'Currency was exchanged successfully'));

                return $this->redirect(['@bill']);
            }
        }

        return $this->render('create-exchange', [
            'model' => $model,
            'canSupport' => $canSupport,
            'rates' => $this->getExchangeRates(),
        ]);
    }

    public function actionGetExchangeRates(?int $client_id = null): array
    {
        $this->response->format = Response::FORMAT_JSON;

        return $this->getExchangeRates($client_id);
    }

    /**
     * @return array
     */
    public function getPaymentTypes()
    {
        return $this->billTypesProvider->getTypesList();
    }

    /**
     * @return array
     */
    private function getTypesAndGroups()
    {
        return $this->billTypesProvider->getGroupedList();
    }

    private function getExchangeRates(?int $client_id = null): array
    {
        $client_id ??= Yii::$app->user->id;

        return Yii::$app->cache->getOrSet(['exchange-rates', $client_id], function () use ($client_id) {
            $currencies = ExchangeRate::find()
                ->select(['from', 'to', 'rate'])
                ->where(['client_id' => $client_id])
                ->all();

            return Sort::by($currencies, static function (ExchangeRate $rate) {
                if ($rate->from === 'EUR' && $rate->to === 'USD') {
                    return 1;
                }
                if ($rate->from === 'USD' && $rate->to === 'EUR') {
                    return 2;
                }

                return INF;
            });
        }, 3600);
    }
}
