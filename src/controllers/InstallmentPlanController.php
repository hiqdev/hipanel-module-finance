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
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\VariantsAction;
use hipanel\actions\ViewAction;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\actions\InstallmentPlanCreateBillAction;
use hipanel\modules\finance\actions\InstallmentPlanProcessAction;
use hipanel\modules\finance\models\InstallmentPlan;
use hipanel\modules\finance\widgets\InstallmentPlanSummaryTable;
use hipanel\widgets\DataProviderGridRenderer;
use hiqdev\hiart\ActiveDataProvider;
use Yii;
use yii\data\ArrayDataProvider;

class InstallmentPlanController extends \hipanel\base\CrudController
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'delete'       => 'installment-plan.delete',
                    'restore'      => 'installment-plan.restore',
                    'process'      => 'installment-plan.process',
                    'create-bill'  => 'bill.create',
                    '*'            => 'sale.read',
                ],
            ],
        ]);
    }

    public function actions(): array
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'responseVariants' => [
                    IndexAction::VARIANT_SUMMARY_RESPONSE => function (VariantsAction $action): string {
                        /** @var ActiveDataProvider $dataProvider */
                        $dataProvider = $action->parent->getDataProvider();
                        $defaultSummary = (new DataProviderGridRenderer($dataProvider))->renderSummary();
                        $installmentPlansSummary = InstallmentPlanSummaryTable::widget([
                            'currencies' => $action->controller->getCurrencyTypes(),
                            'allModels'  => $dataProvider->query->andWhere(['groupby' => 'sum_by_currency'])->all(),
                        ]);

                        return $defaultSummary . $installmentPlansSummary;
                    },
                ],
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel:finance', 'Installment plan has been deleted'),
                'error' => Yii::t('hipanel:finance', 'An error occurred when trying to delete installment plan'),
            ],
            'restore' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel:finance', 'Installment plan has been restored'),
            ],
            'view' => [
                'class' => ViewAction::class,
                'findOptions' => [
                    'with_items' => 1,
                    'with_all_states' => 1,
                ],
                'data' => function ($action) {
                    $model = $action->getModel();

                    return [
                        'itemsDataProvider' => new ArrayDataProvider([
                            'allModels' => $model->getItems(),
                            'pagination' => false,
                        ]),
                    ];
                },
            ],
            'process' => [
                'class' => InstallmentPlanProcessAction::class,
            ],
            'create-bill' => [
                'class' => InstallmentPlanCreateBillAction::class,
            ],
        ]);
    }

    public function actionProcess()
    {
        if (Yii::$app->request->isPost) {
            try {
                InstallmentPlan::perform('process', [], ['batch' => true]);
                Yii::$app->session->setFlash('success', Yii::t('hipanel:finance', 'Installment plans have been processed'));
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->redirect(['index']);
    }
}
