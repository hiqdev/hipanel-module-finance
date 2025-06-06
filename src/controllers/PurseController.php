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
use Exception;
use hipanel\actions\IndexAction;
use hipanel\actions\ProgressAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\RunProcessAction;
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
use RuntimeException;
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
            'generate-acts' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel:finance', 'Document updated'),
            ],
            'generate-and-save-document' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel:finance', 'Document updated'),
            ],
            'calculate' => [
                'class' => ProgressAction::class,
                'onProgress' => function () {
                    $statistic = Costprice::perform('monitor');

                    return ProcessTableGenerator::widget(['statistic' => $statistic]);
                },
            ],
            'recalculate' => [
                'class' => RunProcessAction::class,
                'onRunProcess' => function (RunProcessAction $action) {
                    $request = $action->controller->request;
                    $params = $request->post();
                    $params['month'] = (!empty($params['month'])) ? $params['month'] : date('Y-m-01');
                    Costprice::perform('recalculate', $params);
                },
            ],
            'generation-perform' => [
                'class' => RunProcessAction::class,
                'onRunProcess' => function (RunProcessAction $action) {
                    $request = $action->controller->request;
                    if (!$request->isPost) {
                        return;
                    }
                    $type = $request->post('type');
                    Purse::batchPerform('generate-and-save-all-monthly-documents', [
                        'type' => $type,
                        'client_types' => $type === 'acceptance' ? 'employee' : null,
                    ]);
                }
            ],
            'generation-progress' => [
                'class' => ProgressAction::class,
                'onGettingId' => static fn(ProgressAction $action) => $action->controller->request->get('type'),
                'onProgress' => static function (ProgressAction $action) {
                    $request = $action->controller->request;
                    $type = $request->get('type');
                    $model = new DocumentStatisticModel();
                    $statisticByTypes = DocumentStatisticModel::batchPerform('get-stats',
                        $model->getAttributes([
                            'types',
                            'since',
                        ])
                    );
                    if ($type && in_array($type, explode(',', $model->types), true)) {
                        return StatisticTableGenerator::widget([
                            'type' => $type,
                            'statistic' => $statisticByTypes[$type],
                        ]);
                    }
                },
            ],
        ]);
    }

    public function actionCalculateCostprice(): string
    {
        $statistic = Costprice::perform('monitor');
        $model = new Costprice();

        return $this->render('calculate-costprice', [
            'model' => $model,
            'statistic' => $statistic,
        ]);
    }

    public function actionCostpriceExcelReport(): string
    {
        $model = new Costprice();
        $params = $this->request->post('Costprice', []);
        if (!empty($params)) {
            $params['month'] = (!empty($params['month'])) ? $params['month'] : date('Y-m-01');
            $session = Yii::$app->session;
            try {
                $content = Costprice::perform('reportGenerate', $params);
            } catch (Exception $e) {
                $session->addFlash('error', Yii::t('hipanel:finance', 'Failed to generate the report'));
                throw new RuntimeException(Yii::t('hipanel:finance', 'Failed to generate the report:' . $e->getMessage()));
            }
            $this->response->sendContentAsFile(
                $content,
                $params['type'] . $params['month'] . '.xlsx',
                ['inline' => true, 'mimeType' => 'application/vnd.ms-excel']
            )->send();
        }

        return $this->render('costprice-excel-reports', [
            'model' => $model,
        ]);
    }

    public function actionGenerateAll(): string
    {
        $model = new DocumentStatisticModel();
        $statisticByTypes = DocumentStatisticModel::batchPerform('get-stats',
            $model->getAttributes([
                'types',
                'since',
            ])
        );

        return $this->render('generate-all', ['statisticByTypes' => $statisticByTypes]);
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
            $responseData = $e->getResponse()->getData();
            if (!isset($responseData['_error_ops']['requisite_id']) || !isset($responseData['_error_ops']['type'])) {
                Yii::$app->getSession()->setFlash('error', Yii::t('hipanel:finance', 'Failed to generate document'));
                return $this->redirect(['@client/view', 'id' => $params['client_id']]);
            }

            $contactUrl = Html::a(
                Url::toRoute(['@requisite/view', 'id' => $responseData['_error_ops']['requisite_id']], true),
                ['@requisite/view', 'id' => $responseData['_error_ops']['requisite_id']]
            );
            $type = $responseData['_error_ops']['type'];
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
