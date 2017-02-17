<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\modules\finance\models\Purse;
use Yii;

class PurseController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
            ],
            'update-contact' => [
                'class' => SmartUpdateAction::class,
            ],
            'update-requisite' => [
                'class' => SmartUpdateAction::class,
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'invoice-archive' => [
                'class' => RedirectAction::class,
                'error' => Yii::t('hipanel', 'Under construction'),
            ],
            'generate-and-save-document' => [
                'class'   => SmartPerformAction::class,
                'success' => Yii::t('hipanel:finance', 'Document updated'),
            ],
        ];
    }

    public function actionGenerateDocument($id, $type, $month = null)
    {
        $content_type = 'application/pdf';
        $data = Purse::perform('generate-document', compact('id', 'type', 'month'));
        $response = Yii::$app->getResponse();
        $response->format = $response::FORMAT_RAW;
        $response->getHeaders()->add('content-type', $content_type);

        return $data;
    }
}
