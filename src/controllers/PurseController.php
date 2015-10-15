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

use Yii;
use hipanel\modules\finance\models\Purse;

class PurseController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => 'hipanel\actions\IndexAction',
            ],
            'view' => [
                'class' => 'hipanel\actions\ViewAction',
            ],
            'validate-form' => [
                'class' => 'hipanel\actions\ValidateFormAction',
            ],
            'invoice-archive' => [
                'class' => 'hipanel\actions\RedirectAction',
                'error' => Yii::t('app', 'Under construction'),
            ],
            'update-monthly-invoice' => [
                'class'   => 'hipanel\actions\SmartPerformAction',
                'success' => Yii::t('app', 'Invoice updated'),
            ],
        ];
    }

    public function actionGenerateInvoice($id, $month = null)
    {
        $content_type = 'application/pdf';
        $data = Purse::perform('GenerateMonthlyInvoice', compact('id', 'month'));
        $response = Yii::$app->getResponse();
        $response->format = $response::FORMAT_RAW;
        $response->getHeaders()->add('content-type', $content_type);
        return $data;
    }

}
