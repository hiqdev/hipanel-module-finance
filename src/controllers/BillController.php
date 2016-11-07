<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\OrientationAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\modules\finance\forms\BillImportForm;
use hipanel\modules\finance\models\Bill;
use Yii;
use yii\filters\AccessControl;

class BillController extends \hipanel\base\CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access-bill' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manage', 'deposit'],
                        'actions' => ['index', 'view'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['create-bills'],
                        'actions' => ['create', 'import', 'copy'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['update-bills'],
                        'actions' => ['update'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['delete-bills'],
                        'actions' => ['delete'],
                    ],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return [
            'set-orientation' => [
                'class' => OrientationAction::class,
                'allowedRoutes' => [
                    '@bill/index',
                ],
            ],
            'index' => [
                'class' => IndexAction::class,
                'data' => function ($action) {
                    return [
                        'types' => $action->controller->getPaymentTypes(),
                    ];
                },
            ],
            'view' => [
                'class' => ViewAction::class,
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'create' => [
                'class' => SmartCreateAction::class,
                'data' => function ($action) {
                    list($billTypes, $billGroupLabels) = $this->getTypesAndGroups();

                    return compact('billTypes', 'billGroupLabels');
                },
                'success' => Yii::t('hipanel/finance', 'Payment was created successfully'),
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel/finance', 'Payment was updated successfully'),
                'data' => function ($action) {
                    list($billTypes, $billGroupLabels) = $this->getTypesAndGroups();

                    return compact('billTypes', 'billGroupLabels');
                },
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
                }
            ],
            'delete' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel/finance', 'Payment was deleted successfully'),
            ],
        ];
    }

    public function actionImport()
    {
        $model = new BillImportForm([
            'billTypes' => array_filter($this->getPaymentTypes(), function ($key) {
                // Kick out items that are categories names, but not real types
                return (strpos($key, ',') !== false);
            }, ARRAY_FILTER_USE_KEY)
        ]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $models = $model->parse();

            if ($models !== false) {
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

    /**
     * @return array
     */
    public function getPaymentTypes()
    {
        $options = ['orderby' => 'name_asc'];

        if (Yii::$app->user->can('support')) {
            $options['with_hierarchy'] = true;
        }

        return $this->getRefs('type,bill', 'hipanel/finance', $options);
    }

    /**
     * @return array
     */
    private function getTypesAndGroups()
    {
        $billTypes = [];
        $billGroupLabels = [];

        $types = $this->getPaymentTypes();

        foreach ($types as $key => $title) {
            list($type, $name) = explode(',', $key);

            if (!isset($billTypes[$type])) {
                $billTypes[$type] = [];
                $billGroupLabels[$type] = ['label' => $title];
            }

            if (isset($name)) {
                foreach ($types as $k => $t) {
                    if (strpos($k, $type . ',') === 0) {
                        $billTypes[$type][$k] = $t;
                    }
                }
            }
        }

        return [$billTypes, $billGroupLabels];
    }
}
