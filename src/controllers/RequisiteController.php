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
use hipanel\actions\SmartPerformAction;
use hipanel\actions\ViewAction;
use hipanel\actions\ComboSearchAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\PrepareBulkAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\ProxyAction;
use hipanel\filters\EasyAccessControl;
use hipanel\actions\ValidateFormAction;
use hipanel\base\CrudController;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\client\actions\ContactCreateAction;
use hipanel\modules\client\models\query\ContactQuery;
use hipanel\modules\finance\actions\CdbExportAction;
use hipanel\modules\finance\models\Requisite;
use yii\base\Event;
use Yii;

class RequisiteController extends CrudController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'reserve-number' => 'requisites.update',
                    'create' => 'requisites.create',
                    'copy' => 'requisites.create',
                    'update' => 'requisites.update',
                    'set-templates' => 'requisites.update',
                    'bulk-set-templates' => 'requisites.update',
                    'set-serie' => 'requisites.update',
                    'cdb-export' => 'bill.update',
                    '*' => 'requisites.read',
                ],
            ],
        ]);
    }

    public function actions()
    {
        $canSeeDocuments = Yii::getAlias('@document', false) && Yii::$app->user->can('document.read');
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'on beforePerform' => function (Event $event) use ($canSeeDocuments) {
                    $action = $event->sender;
                    $representation = $action->controller->indexPageUiOptionsModel->representation;
                    $query = $action->getDataProvider()->query;
                    if (in_array($representation, ['balance', 'balances'], true)) {
                        $query->addSelect('balances');
                    }
                    if ($canSeeDocuments) {
                        $query->withDocuments();
                    }
                },
            ],
            'search' => [
                'class' => ComboSearchAction::class,
            ],
            'create' => [
                'class' => ContactCreateAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
                'findOptions' => ['with_counters' => 1],
                'on beforePerform' => function ($event) use ($canSeeDocuments) {
                    /** @var ViewAction $action */
                    $action = $event->sender;

                    /** @var ContactQuery $query */
                    $query = $action->getDataProvider()->query;

                    if ($canSeeDocuments) {
                        $query->withDocuments();
                    }

                    $query->addSelect('balances');
                    $query->andWhere(['show_nonrequisite' => 1]);
                    $query->withLocalizations();
                },
            ],
            'reserve-number' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel:finance', 'Document number was reserved'),
                'view' => 'modal/reserveNumber',
                'POST html' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function () {
                            $requisite = Yii::$app->request->post('Requisite');

                            return ['@requisite/view', 'id' => $requisite['id']];
                        },
                    ],
                ],
            ],
            'set-templates' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel:finance', 'Templates changed'),
                'POST html' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function ($action) {
                            return Yii::$app->request->referrer;
                        },
                    ],
                ],
            ],
            'bulk-set-templates' => [
                'class' => SmartUpdateAction::class,
                'scenario' => 'set-templates',
                'view' => 'modal/_bulkSetTemplates',
                'success' => Yii::t('hipanel:finance', 'Templates changed'),
                'POST' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function ($action) {
                            return Yii::$app->request->referrer;
                        },
                    ],
                ],
                'collectionLoader' => function ($action) {
                    /** @var SmartPerformAction $action */
                    $data = Yii::$app->request->post($action->collection->getModel()->formName());
                    $attributes = [];
                    foreach (Requisite::getTemplatesTypes() as $attribute) {
                        $attributes["{$attribute}_id"] = $data["{$attribute}_id"];
                        unset($data["{$attribute}_id"]);
                    }

                    foreach ($data as &$item) {
                        $item = array_merge($item, $attributes);
                    }

                    $action->collection->load($data);
                },
                'on beforeFetch' => function (Event $event) {
                    /** @var \hipanel\actions\SearchAction $action */
                    $action = $event->sender;
                    $dataProvider = $action->getDataProvider();
                    $dataProvider->query
                        ->select(['*'])
                        ->addSelect(['templates'])
                        ->andWhere(['show_nonrequisite' => 1]);
                },
            ],
            'set-templates-modal' => [
                'class' => PrepareBulkAction::class,
                'view' => 'modal/_bulkSetTemplates',
                'on beforePerform' => function (Event $event) {
                    /** @var \hipanel\actions\SearchAction $action */
                    $action = $event->sender;
                    $dataProvider = $action->getDataProvider();
                    $dataProvider->query
                        ->select(['*'])
                        ->addSelect(['templates']);
                },
            ],
            'set-serie' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel:finance', 'Serie changed'),
                'error' => Yii::t('hipanel:finance', 'Failed to change requisite serie'),
            ],
            'bulk-set-serie' => [
                'class' => SmartUpdateAction::class,
                'scenario' => 'set-serie',
                'view' => 'modal/_bulkSetSerie',
                'success' => Yii::t('hipanel:finance', 'Series changed'),
                'collectionLoader' => function ($action) {
                    /** @var SmartPerformAction $action */
                    $data = Yii::$app->request->post($action->collection->getModel()->formName());
                    $serie = $data['serie'];
                    unset($data['serie']);
                    foreach ($data as &$item) {
                        $item['serie'] = $serie;
                    }

                    $action->collection->load($data);
                },
                'POST pjax' => [
                    'save' => true,
                    'success' => [
                        'class' => ProxyAction::class,
                        'action' => 'index',
                    ],
                ],
                'on beforeFetch' => function (Event $event) {
                    /** @var \hipanel\actions\SearchAction $action */
                    $action = $event->sender;
                    $dataProvider = $action->getDataProvider();
                    $dataProvider->query
                        ->select(['*']);
                },
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'cdb-export' => [
                'class' => CdbExportAction::class,
            ]
        ]);
    }
}
