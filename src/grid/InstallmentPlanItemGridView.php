<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\modules\finance\models\InstallmentPlanItem;
use Yii;
use yii\helpers\Html;

class InstallmentPlanItemGridView extends BoxedGridView
{
    public function columns(): array
    {
        return array_merge(parent::columns(), [
            'no' => [
                'attribute' => 'no',
                'label' => Yii::t('hipanel', '#'),
                'filter' => false,
                'headerOptions' => ['class' => 'text-center', 'style' => 'width:40px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            'month' => [
                'attribute' => 'month',
                'label' => Yii::t('hipanel', 'Month'),
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlanItem $model) => Html::encode(substr($model->month, 0, 7)),
            ],
            'sum' => [
                'attribute' => 'sum',
                'label' => Yii::t('hipanel:finance', 'Sum'),
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right text-bold'],
                'value' => fn(InstallmentPlanItem $model) => Html::encode($model->sum . ' ' . strtoupper($model->currency)),
            ],
            'charge_sum' => [
                'attribute' => 'charge_sum',
                'label' => Yii::t('hipanel:finance', 'Charged'),
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => fn(InstallmentPlanItem $model) => [
                    'class' => 'text-right' . ($model->isPaid() ? ' text-success' : ' text-muted'),
                ],
                'value' => fn(InstallmentPlanItem $model) => $model->isPaid()
                    ? Html::encode($model->charge_sum . ' ' . strtoupper($model->currency))
                    : Html::tag('span', Yii::t('hipanel', 'Pending'), ['class' => 'text-muted']),
            ],
            'charge_id' => [
                'attribute' => 'charge_id',
                'label' => Yii::t('hipanel:finance', 'Charge'),
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlanItem $model) => $model->charge_id && Yii::$app->user->can('bill.charges.read')
                    ? Html::a('#' . $model->charge_id, ['@charge/view', 'id' => $model->charge_id])
                    : ($model->charge_id ? '#' . $model->charge_id : '—'),
            ],
            'bill_id' => [
                'attribute' => 'bill_id',
                'label' => Yii::t('hipanel:finance', 'Bill'),
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlanItem $model) => $model->bill_id && Yii::$app->user->can('bill.read')
                    ? Html::a('#' . $model->bill_id, ['@bill/view', 'id' => $model->bill_id])
                    : ($model->bill_id ? '#' . $model->bill_id : '—'),
            ],
            'installment_plan_id' => [
                'attribute' => 'installment_plan_id',
                'label' => Yii::t('hipanel:finance', 'Installment plan'),
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlanItem $model) => $model->installment_plan_id
                    ? Html::a('#' . $model->installment_plan_id, ['@installment-plan/view', 'id' => $model->installment_plan_id])
                    : '—',
            ],
            'tariff_link' => [
                'attribute' => 'tariff',
                'format' => 'raw',
                'value' => function (InstallmentPlanItem $model) {
                    return $this->tariffLink($model);
                },
            ],
            'formula' => [
                'attribute' => 'formula',
                'label' => Yii::t('hipanel:finance', 'Formula'),
                'filter' => false,
                'value' => fn(InstallmentPlanItem $model) => $model->formula ?: '—',
            ],
        ]);
    }

    public function tariffLink(InstallmentPlanItem $model): ?string
    {
        $canSeeLink = Yii::$app->user->can('plan.read');
        $tariff = Html::encode($model->tariff);

        return $canSeeLink ? Html::a($tariff, ['@plan/view', 'id' => $model->tariff_id]) : $tariff;
    }
}
