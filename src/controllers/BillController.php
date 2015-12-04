<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
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
                'success'   => Yii::t('app', 'Bill created'),
            ],
            'update' => [
                'class'     => SmartUpdateAction::class,
                'success'   => Yii::t('app', 'Bill updated'),
            ],
            'delete' => [
                'class'     => SmartPerformAction::class,
                'success'   => Yii::t('app', 'Bill deleted'),
            ],
        ];
    }

    public function getPaymentType()
    {
        return Ref::getList('type,bill,deposit', ['with_hierarchy' => true]);
    }
}
