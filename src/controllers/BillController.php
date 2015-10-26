<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (https://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\models\Ref;
use Yii;

class BillController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
            'index' => [
                'class'     => 'hipanel\actions\IndexAction',
                'data'      => function ($action) {
                    return [
                        'type' => $action->controller->getPaymentType(),
                    ];
                }
            ],
            'view' => [
                'class'     => 'hipanel\actions\ViewAction',
            ],
            'validate-form' => [
                'class'     => 'hipanel\actions\ValidateFormAction',
            ],
            'create' => [
                'class'     => 'hipanel\actions\SmartCreateAction',
                'success'   => Yii::t('app', 'Bill created'),
            ],
            'update' => [
                'class'     => 'hipanel\actions\SmartUpdateAction',
                'success'   => Yii::t('app', 'Bill updated'),
            ],
            'delete' => [
                'class'     => 'hipanel\actions\SmartPerformAction',
                'success'   => Yii::t('app', 'Bill deleted'),
            ],
        ];
    }

    public function getPaymentType()
    {
        return Ref::getList('type,bill,deposit', ['with_hierarchy' => true]);
    }
}
