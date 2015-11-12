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

use Yii;

class TariffController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
            'index' => [
                'class'     => 'hipanel\actions\IndexAction',
            ],
            'view' => [
                'class'     => 'hipanel\actions\ViewAction',
            ],
            'validate-form' => [
                'class'     => 'hipanel\actions\ValidateFormAction',
            ],
            'create' => [
                'class'     => 'hipanel\actions\SmartCreateAction',
                'success'   => Yii::t('app', 'Tariff created'),
            ],
            'set-note' => [
                'class'     => 'hipanel\actions\SmartUpdateAction',
                'success'   => Yii::t('app', 'Note updated'),
            ],
            'update' => [
                'class'     => 'hipanel\actions\SmartUpdateAction',
                'success'   => Yii::t('app', 'Tariff updated'),
            ],
            'delete' => [
                'class'     => 'hipanel\actions\SmartPerformAction',
                'success'   => Yii::t('app', 'Tariff deleted'),
            ],
        ];
    }
}
