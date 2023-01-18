<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\document\models\Statistic as DocumentStatisticModel;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\widgets\StatisticTableGenerator;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\Event;

class PurseController extends \hipanel\base\CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'update,update-requisite,update-contact' => 'purse.update',
                    'pre-generate-document,generate-monthly-document,generate-document' => 'document.generate',
                    '*' => 'bill.read',
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'on beforePerform' => static function (Event $event): void {
                    /** @var \hipanel\actions\SearchAction $action */
                    $action = $event->sender;
                    $query = $action->getDataProvider()->query;
                    $query->joinWith(['contact', 'requisite'])->addSelect(['*', 'contact', 'requisite']);
                },
            ],
            'view' => [
                'class' => ViewAction::class,
            ],
            'create' => [
                'class' => SmartCreateAction::class,
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
            'generate-and-save-monthly-document' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel:finance', 'Document updated'),
            ],
            'generate-and-save-document' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel:finance', 'Document updated'),
            ],
        ]);
    }

    public function actionGenerateAll()
    {
        $request = Yii::$app->request;
        $model = new DocumentStatisticModel();
        $statisticByTypes = DocumentStatisticModel::batchPerform('get-stats', $model->getAttributes([
            'types',
            'since',
        ]));

        $type = $request->post('type');
        if ($request->isAjax && $type && in_array($type, explode(',', $model->types), true)) {
            return StatisticTableGenerator::widget(['type' => $type, 'statistic' => $statisticByTypes[$type]]);
        } else {
            if ($request->isPost) {
                try {
                    session_write_close();
                    Purse::batchPerform('generate-and-save-all-monthly-documents', [
                        'type' => $type,
                        'client_types' => $type === 'acceptance' ? 'employee' : null,
                    ]);
                } catch (ResponseErrorException $e) {
                    Yii::$app->getSession()->setFlash('error', Yii::t('hipanel:finance', 'Failed to generate document! Check requisites!'));
                }
            }

            return $this->render('generate-all', ['statisticByTypes' => $statisticByTypes]);
        }
    }

    public function actionGenerateMonthlyDocument($id, $type, $client_id, $month = null)
    {
        return $this->generateDocument('generate-monthly-document', compact('id', 'type', 'client_id', 'month'));
    }

    public function actionGenerateDocument($id, $type)
    {
        return $this->generateDocument('generate-document', compact('id', 'type'));
    }

    public function generateDocument($action, $params)
    {
        try {
            $data = Purse::perform($action, $params);
        } catch (ResponseErrorException $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('hipanel:finance', 'Failed to generate document! Check requisites!'));

            return $this->redirect(['@client/view', 'id' => $params['client_id']]);
        }
        $this->asPdf();

        return $data;
    }

    protected function asPdf()
    {
        $response = Yii::$app->getResponse();
        $response->format = $response::FORMAT_RAW;
        $response->getHeaders()->add('content-type', 'application/pdf');
    }

    public function actionPreGenerateDocument($type, $client_id)
    {
        $purse = new Purse(['scenario' => 'generate-and-save-monthly-document']);
        if ($purse->load(Yii::$app->request->post()) && $purse->validate()) {
            return $this->redirect([
                '@purse/generate-monthly-document',
                'id' => $purse->id,
                'type' => $type,
                'client_id' => $client_id,
                'month' => $purse->month,
            ]);
        }
    }
}
