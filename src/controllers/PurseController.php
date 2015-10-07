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
            'generate-invoice' => [
                'class'   => 'hipanel\actions\RedirectAction',
                'success' => Yii::t('app', 'Invoice generated'),
            ],
            'invoice-archive' => [
                'class' => 'hipanel\actions\RedirectAction',
                'error' => Yii::t('app', 'Under construction'),
            ],
            'pdf-invoice2' => [
                'class'   => 'hipanel\actions\SmartUpdateAction',
                'success' => Yii::t('app', 'Tariff updated'),
            ],
        ];
    }

    public function actionPdfInvoice($id, $month)
    {
        //https://hiapi.advancedhosters.com/purseGenerateMonthlyInvoice?auth_login=sol&auth_password=xfgrfnhzv&month=2015-09-01&id=310256944
        $content_type = 'application/pdf';
        $data = Purse::perform('GenerateMonthlyInvoice', ['id' => $id, 'month' => $month]);
        $response = Yii::$app->getResponse();
        $response->format = $response::FORMAT_RAW;
        $response->getHeaders()->add('content-type', $content_type);
        return $data;
    }

}
