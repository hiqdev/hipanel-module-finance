<?php

/*
 * Finance Plugin for HiPanel
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
use Yii;

class TariffController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
            'index' => [
                'class'     => IndexAction::class,
            ],
            'view' => [
                'class'     => ViewAction::class,
            ],
            'validate-form' => [
                'class'     => ValidateFormAction::class,
            ],
            'create' => [
                'class'     => SmartCreateAction::class,
                'success'   => Yii::t('app', 'Tariff created'),
            ],
            'set-note' => [
                'class'     => SmartUpdateAction::class,
                'success'   => Yii::t('app', 'Note updated'),
            ],
            'update' => [
                'class'     => SmartUpdateAction::class,
                'success'   => Yii::t('app', 'Tariff updated'),
            ],
            'delete' => [
                'class'     => SmartPerformAction::class,
                'success'   => Yii::t('app', 'Tariff deleted'),
            ],
        ];
    }
}
