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
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\models\Ref;
use Yii;

class BillController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
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
                    $types = Ref::getList('type,bill', ['with_hierarchy' => 1, 'orderby' => 'name_asc']);
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
        return Ref::getList('type,bill', Yii::$app->user->can('support') ? ['with_hierarchy' => true] : []);
    }
}
