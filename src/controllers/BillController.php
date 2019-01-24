<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\Action;
use hipanel\actions\IndexAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\client\controllers\ContactController;
use hipanel\modules\finance\actions\BillManagementAction;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillImportForm;
use hipanel\modules\finance\forms\CurrencyExchangeForm;
use hipanel\modules\finance\helpers\ChargesGrouper;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\ExchangeRate;
use hipanel\modules\finance\models\Resource;
use hiqdev\hiart\Collection;
use hipanel\modules\finance\providers\BillTypesProvider;
use hiqdev\hiart\ActiveQuery;
use Yii;
use yii\base\Event;
use yii\base\Module;

class BillController extends \hipanel\base\CrudController
{
    /**
     * @var BillTypesProvider
     */
    private $billTypesProvider;

    public function __construct($id, Module $module, BillTypesProvider $billTypesProvider, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->billTypesProvider = $billTypesProvider;
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access-bill' => [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'create,import,copy'    => 'bill.create',
                    'create-exchange'       => 'bill.create',
                    'update,charge-delete'  => 'bill.update',
                    'delete'                => 'bill.delete',
                    '*'                     => 'bill.read',
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'data' => function ($action) {
                    list($billTypes, $billGroupLabels) = $this->getTypesAndGroups();
                    $rates = $this->getExchangeRates();

                    return compact('billTypes', 'billGroupLabels', 'rates');
                },
            ],
            'view' => [
                'class' => ViewAction::class,
                'on beforePerform' => function (Event $event) {
                    /** @var \hipanel\actions\SearchAction $action */
                    $action = $event->sender;
                    $dataProvider = $action->getDataProvider();
                    $dataProvider->query
                        ->joinWith(['charges' => function (ActiveQuery $query) {
                            $query->joinWith('commonObject');
                            $query->joinWith('latestCommonObject');
                        }])
                        ->andWhere(['with_charges' => true]);
                },
                'data' => function (Action $action, array $data) {
                    return array_merge($data, [
                        'grouper' => new ChargesGrouper($data['model']->charges),
                    ]);
                },
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'create' => [
                'class' => BillManagementAction::class,
            ],
            'update' => [
                'class' => BillManagementAction::class,
            ],
            'copy' => [
                'class' => SmartUpdateAction::class,
                'scenario' => 'create',
                'data' => function ($action, $data) {
                    foreach ($data['models'] as $model) {
                        /** @var Bill $model */
                        $model->prepareToCopy();
                    }

                    list($billTypes, $billGroupLabels) = $this->getTypesAndGroups();

                    return compact('billTypes', 'billGroupLabels');
                },
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
        $model = new BillImportForm([
            'billTypes' => array_filter($this->getPaymentTypes(), function ($key) {
                // Kick out items that are categories names, but not real types
                return strpos($key, ',') !== false;
            }, ARRAY_FILTER_USE_KEY),
        ]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $models = $model->parse();

            if ($models !== false) {
                $models = BillForm::createMultipleFromBills($models, 'create');
                list($billTypes, $billGroupLabels) = $this->getTypesAndGroups();

                return $this->render('create', [
                    'models' => $models,
                    'model' => reset($models),
                    'billTypes' => $billTypes,
                    'billGroupLabels' => $billGroupLabels,
                ]);
            }
        }

        return $this->render('import', ['model' => $model]);
    }

    public function actionCreateExchange()
    {
        $model = new CurrencyExchangeForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($id = $model->save()) {
                Yii::$app->session->addFlash('success', Yii::t('hipanel:finance', 'Currency was exchanged successfully'));
                return $this->redirect(['@bill']);
            }
        }

        return $this->render('create-exchange', [
            'model' => $model,
            'rates' => $this->getExchangeRates()
        ]);
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

    private function getExchangeRates()
    {
        return Yii::$app->cache->getOrSet(['exchange-rates', Yii::$app->user->id], function () {
            return ExchangeRate::find()->select(['from', 'to', 'rate'])->all();
        }, 3600);
    }
}
