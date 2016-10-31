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
                        'actions' => ['create'],
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
                    '@bill/index'
                ],
            ],
            'index' => [
                'class'     => IndexAction::class,
                'data'      => function ($action) {
                    return [
                        'type' => $action->controller->getPaymentType(),
                    ];
                },
            ],
            'view' => [
                'class'     => ViewAction::class,
            ],
            'validate-form' => [
                'class'     => ValidateFormAction::class,
            ],
            'create' => [
                'class'     => SmartCreateAction::class,
                'data' => function ($action) {
                    $types = $this->getRefs('type,bill', 'hipanel/finance', ['with_hierarchy' => 1, 'orderby' => 'name_asc']);
                    $billTypes = [];
                    $billGroupLabels = [];

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

                    return ['billTypes' => $billTypes, 'billGroupLabels' => $billGroupLabels];
                },
                'success'   => Yii::t('hipanel/finance', 'Bill was created successfully'),
            ],
            'update' => [
                'class'     => SmartUpdateAction::class,
                'success'   => Yii::t('hipanel/finance', 'Bill was updated successfully'),
            ],
            'delete' => [
                'class'     => SmartPerformAction::class,
                'success'   => Yii::t('hipanel/finance', 'Bill was deleted successfully'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getPaymentType()
    {
        return $this->getRefs('type,bill', 'hipanel/finance', Yii::$app->user->can('support') ? ['with_hierarchy' => true] : []);
    }
}
