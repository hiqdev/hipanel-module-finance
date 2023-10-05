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

use advancedhosters\hipanel\modules\costprice\models\Costprice;
use hipanel\actions\IndexAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\components\Response;
use hipanel\filters\EasyAccessControl;
use hipanel\helpers\Url;
use hipanel\modules\document\models\Statistic as DocumentStatisticModel;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\widgets\ProcessTableGenerator;
use hipanel\modules\finance\widgets\StatisticTableGenerator;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\Event;
use yii\helpers\Html;

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
                    'calculate-costprice' => 'costprice.read',
                    'finance-tools' => ['document.generate-all', 'costprice.read'],
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

    public function actionRecalculate()
    {
        $request = $this->request;
        if ($request->isAjax) {
            $params = $request->post();
            $params['month'] = (!empty($params['month'])) ? $params['month'] : date('Y-m-01');
            ignore_user_abort(true);
            ini_set('memory_limit', '2G');

            ob_start();

            header('Connection: close');
            header('Content-Length: ' . ob_get_length());
            ob_end_flush();
            @ob_flush();
            flush();
            fastcgi_finish_request(); // required for PHP-FPM (PHP > 5.3.3)
            Costprice::perform('recalculate', $params);
        }
        die();
    }

    public function actionCalculateCostprice()
    {
        $request = $this->request;
        $statistic = Costprice::perform('monitor');
        $model = new Costprice();
        if ($request->isAjax) {
            return ProcessTableGenerator::widget(['statistic' => $statistic]);
        }

        return $this->render('calculate-costprice', [
            'model' => $model,
            'statistic' => $statistic,
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
                    Yii::$app->getSession()->setFlash('error', Yii::t('hipanel:finance', "Failed find templates for this requisite and document's type. Please, set templates for requisite"));
                }
            }

            return $this->render('generate-all', ['statisticByTypes' => $statisticByTypes]);
        }
    }

    public function actionGenerateMonthlyDocument()
    {
        return $this->generateDocument('generate-monthly-document', $this->request->get());
    }

    public function actionGenerateDocument($id, $type)
    {
        return $this->generateDocument('generate-document', ['id' => $id, 'type' => $type]);
    }

    public function generateDocument($action, $params)
    {
        try {
            $content = Purse::perform($action, $params);
        } catch (ResponseErrorException $e) {
            $contactUrl = Html::a(Url::toRoute(['@requisite/view', 'id' => $e->getResponse()->getData()['_error_ops']['requisite_id']], true),
                ['@requisite/view', 'id' => $e->getResponse()->getData()['_error_ops']['requisite_id']]);
            $type = $e->getResponse()->getData()['_error_ops']['type'];

            if (Yii::$app->user->can('requisites.update')) {
                Yii::$app->getSession()->setFlash('error',
                    Yii::t('hipanel:finance',
                        "No templates for requisite. Follow this link {contactUrl} and set template of type '{type}'", [
                            'contactUrl' => $contactUrl,
                            'type' => $type,
                        ]));
            } else {
                Yii::$app->getSession()->setFlash('error',
                    Yii::t('hipanel:finance', 'No templates for requisite. Please contact finance department'));
            }

            return $this->redirect(['@client/view', 'id' => $params['client_id']]);
        }
        $this->asPdf();

        return $content;
    }

    protected function asPdf(): void
    {
        $this->response->format = Response::FORMAT_RAW;
        $this->response->getHeaders()->add('content-type', 'application/pdf');
    }

    public function actionPreGenerateDocument($type, $client_id)
    {
        $purse = new Purse(['scenario' => 'generate-and-save-monthly-document']);
        if ($purse->load($this->request->post()) && $purse->validate()) {
            $payload = array_merge([
                '@purse/generate-monthly-document',
                'type' => $type,
                'client_id' => $client_id,
            ], $purse->toArray());

            return $this->redirect($payload);
        }
    }
}
