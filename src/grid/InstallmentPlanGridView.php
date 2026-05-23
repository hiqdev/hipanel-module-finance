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

use hipanel\grid\ActionColumn;
use hipanel\grid\BoxedGridView;
use hipanel\grid\CurrencyColumn;
use hipanel\modules\finance\models\InstallmentPlan;
use hipanel\modules\finance\widgets\combo\InstallmentPlanStateCombo;
use hipanel\modules\stock\grid\CompanyColumn;
use hipanel\modules\stock\grid\WarrantyColumn;
use hipanel\modules\server\widgets\combo\DeviceCombo;
use hipanel\widgets\gridLegend\GridLegend;
use Yii;
use yii\helpers\Html;

class InstallmentPlanGridView extends BoxedGridView
{
    public function columns(): array
    {
        return array_merge(parent::columns(), [
            'view_link' => [
                'label' => '',
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:1%'],
                'contentOptions' => function ($model) {
                    $legend = new InstallmentPlanGridLegend($model);
                    foreach ($legend->items() as $item) {
                        if ($item['rule']) {
                            return [
                                'style' => "background-color: {$item['color']} !important;",
                            ];
                        }
                    }

                    return [];
                },
                'value' => fn(InstallmentPlan $model) => Html::a(
                    '<i class="fa fa-bars"></i>',
                    ['@installment-plan/view', 'id' => $model->id],
                    ['class' => 'btn btn-default btn-xs', 'title' => Yii::t('hipanel', 'Details'), 'data-toggle' => 'tooltip'],
                ),
            ],
            'serialno' => [
                'label' => Yii::t('hipanel:finance', 'Serial'),
                'filterOptions' => ['class' => 'narrow-filter'],
                'filterAttribute' => 'serialno_inilike',
                'format' => 'raw',
                'value' => fn(InstallmentPlan $model) => Html::a(Html::encode($model->serialno), ['@part/view', 'id' => $model->part_id], ['class' => 'text-bold']),
            ],
            'model' => [
                'filterAttribute' => 'partno_inilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'raw',
                'label' => Yii::t('hipanel:finance', 'Part No.'),
                'value' => static function (InstallmentPlan $model): string {
                    $partNo = Html::encode($model->partno);
                    if (Yii::$app->user->can('model.read')) {
                        return Html::a($partNo, ['@model/view', 'id' => $model->model_id], [
                            'data' => ['toggle' => 'tooltip'],
                            'title' => Html::encode(sprintf(
                                "%s %s",
                                $model->part_type,
                                $model->brand,
                            )),
                        ]);
                    }

                    return $partNo;
                },
            ],
            'device' => [
                'filterAttribute' => 'device_like',
                'filter' => static function ($column, $model, $attribute) {
                    return DeviceCombo::widget([
                        'model' => $model,
                        'attribute' => $column->getFilterAttribute(),
                        'formElementSelector' => 'td',
                    ]);
                },
                'format' => 'raw',
                'value' => static function (InstallmentPlan $model) {
                    return Html::tag('b', Html::encode($model->device), ['style' => 'margin-left:1em']);
                },
            ],
            'tariff_link' => [
                'attribute' => 'tariff',
                'format' => 'raw',
                'value' => function (InstallmentPlan $model) {
                    return $this->tariffLink($model);
                },
            ],
            'state' => [
                'filterAttribute' => 'state',
                'filter' => static function ($column, $model, $attribute) {
                    return InstallmentPlanStateCombo::widget([
                        'model' => $model,
                        'attribute' => $attribute,
                        'formElementSelector' => 'td',
                    ]);
                },
                'format' => 'raw',
                'value' => static function (InstallmentPlan $model) {
                    $labelClass = match ($model->state) {
                        InstallmentPlan::STATE_FINISHED   => 'label-success',
                        InstallmentPlan::STATE_PAID_EARLY => 'label-success',
                        InstallmentPlan::STATE_ONGOING    => 'label-info',
                        InstallmentPlan::STATE_BUYOUT     => 'label-warning',
                        InstallmentPlan::STATE_ADJOURNED  => 'label-default',
                        default                           => 'label-danger',
                    };

                    return Html::tag('span', Html::encode($model->state_name), ['class' => "label {$labelClass}"]);
                },
            ],
            'expected_sum' => [
                'class' => CurrencyColumn::class,
                'filter' => false,
                'attribute' => 'expected_sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function (InstallmentPlan $model) {
                    return ['class' => 'text-right' . ($model->expected_sum > 0 ? ' text-bold' : '')];
                },
                'exportedColumns' => ['export_expected_sum'],
            ],
            'expected_monthly_sum' => [
                'class' => CurrencyColumn::class,
                'filter' => false,
                'attribute' => 'expected_monthly_sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function (InstallmentPlan $model) {
                    return ['class' => 'text-right' . ($model->expected_monthly_sum > 0 ? ' text-bold' : '')];
                },
                'exportedColumns' => ['export_expected_monthly_sum'],
            ],
            'left_sum' => [
                'class' => CurrencyColumn::class,
                'filter' => false,
                'attribute' => 'left_sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function (InstallmentPlan $model) {
                    return ['class' => 'text-right' . ($model->left_sum > 0 ? ' text-bold' : '')];
                },
                'exportedColumns' => ['export_left_sum'],
            ],
            'charged_sum' => [
                'filter' => false,
                'attribute' => 'charged_sum',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function (InstallmentPlan $model) {
                    return ['class' => 'text-right' . ($model->charged_sum > 0 ? ' text-bold' : '')];
                },
                'format' => 'raw',
                'value' => function (InstallmentPlan $model) {
                    $sum = $this->formatter->asCurrency($model->charged_sum, $model->currency);
                    if ($model->charged_sum > 0) {
                        $sum = Html::tag('b', $sum);
                    }
                    $url = $this->getChargeUrl($model);
                    if (!$url) {
                        return Html::tag('span', $sum, ['class' => 'text-right' . ($model->left_sum > 0 ? ' text-bold' : '')]);
                    }

                    $icon = Html::tag('i', '', ['class' => 'fa fa-list']);

                    $sum = Html::a($sum, $url);
                    $icon = Html::a($icon, $url, [
                        'class' => 'btn btn-default btn-xs',
                        'title' => Yii::t('hipanel:finance', 'Charges'),
                        'data-toggle' => 'tooltip',
                        'style' => 'margin-left: .5em',
                    ]);
                    return Html::tag('span', $sum . $icon, ['style' => 'display: flex; justify-content: flex-end; align-items: center;']);
                },
                'exportedColumns' => ['export_charged_sum', 'export_currency'],
            ],
            'export_expected_sum' => [
                'label' => Yii::t('hipanel:finance', 'Total sum'),
                'format' => static fn(float $value): float => $value,
                'value' => static fn(InstallmentPlan $model): float => (float)$model->expected_sum,
            ],
            'export_expected_monthly_sum' => [
                'label' => Yii::t('hipanel:finance', 'Monthly sum'),
                'format' => static fn(float $value): float => $value,
                'value' => static fn(InstallmentPlan $model): float => (float)$model->expected_monthly_sum,
            ],
            'export_left_sum' => [
                'label' => Yii::t('hipanel:finance', 'Left sum'),
                'format' => static fn(float $value): float => $value,
                'value' => static fn(InstallmentPlan $model): float => (float)$model->left_sum,
            ],
            'export_charged_sum' => [
                'label' => Yii::t('hipanel:finance', 'Charged sum'),
                'format' => static fn(float $value): float => $value,
                'value' => static fn(InstallmentPlan $model): float => (float)$model->charged_sum,
            ],
            'export_currency' => [
                'label' => Yii::t('hipanel:finance', 'Currency'),
                'value' => static fn(InstallmentPlan $model): string => strtoupper((string)$model->currency),
            ],
            'quantity' => [
                'filter' => false,
            ],
            'since' => [
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlan $model) => $model->since
                    ? Html::encode((new \DateTimeImmutable($model->since))->format('Y-m-d'))
                    : '—',
            ],
            'till' => [
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlan $model) => $model->till
                    ? Html::encode((new \DateTimeImmutable($model->till))->format('Y-m-d'))
                    : '—',
            ],
            'company_id' => [
                'class' => CompanyColumn::class,
                'visible' => Yii::$app->user->can('order.read'),
            ],
            'order_name' => [
                'attribute' => 'order_id',
                'filterAttribute' => 'order_id',
                'filter' => function ($column, $model, $attribute) {
                    return \hipanel\modules\stock\widgets\combo\OrderCombo::widget([
                        'model' => $model,
                        'attribute' => $attribute,
                        'formElementSelector' => 'td',
                    ]);
                },
                'filterOptions' => ['class' => 'narrow-filter'],
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'format' => 'raw',
                'visible' => Yii::$app->user->can('order.read') && Yii::$app->user->can('owner-staff'),
                'value' => function (InstallmentPlan $model): string {
                    return Html::a(Html::encode($model->order_name), ['@order/view', 'id' => $model->order_id]);
                },
            ],
            'parent_id' => [
                'attribute' => 'parent_id',
                'label' => Yii::t('hipanel:finance', 'Parent plan'),
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlan $model) => $model->parent_id
                    ? Html::a('#' . $model->parent_id, ['@installment-plan/view', 'id' => $model->parent_id])
                    : '—',
            ],
            'child_id' => [
                'attribute' => 'child_id',
                'label' => Yii::t('hipanel:finance', 'Child plan'),
                'filter' => false,
                'format' => 'raw',
                'value' => fn(InstallmentPlan $model) => $model->child_id
                    ? Html::a('#' . $model->child_id, ['@installment-plan/view', 'id' => $model->child_id])
                    : '—',
            ],
            'note' => [
                'attribute' => 'note',
                'label' => Yii::t('hipanel', 'Note'),
                'filter' => false,
                'value' => fn(InstallmentPlan $model) => $model->note ?: '—',
            ],
            'warranty_till' => [
                'class' => WarrantyColumn::class,
                'attribute' => 'warranty_till',
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'format' => ['datetime', 'php:Y-m-d'],
            ],
            'actions' => [
                'class' => ActionColumn::class,
                'template' => '{delete} {restore}',
                'visibleButtonsCount' => 2,
                'visible' => Yii::$app->user->can('installment-plan.delete') || Yii::$app->user->can('installment-plan.restore') || Yii::$app->user->can('installment-plan.update'),
                'visibleButtons' => [
                    'delete'  => fn(InstallmentPlan $model) => Yii::$app->user->can('installment-plan.delete') && !$model->isDeleted(),
                    'restore' => fn(InstallmentPlan $model) => Yii::$app->user->can('installment-plan.restore') && $model->isDeleted(),
                ],
                'buttons' => [
                    'delete' => function ($url, InstallmentPlan $model) {
                        return Html::a(
                            '<i class="fa fa-trash"></i>&nbsp;' . Yii::t('hipanel', 'Delete'),
                            $url,
                            [
                                'class' => 'btn btn-default btn-xs',
                                'data' => [
                                    'method' => 'POST',
                                    'pjax'   => '0',
                                    'confirm' => Yii::t('hipanel:finance', 'Are you sure you want to delete this installment plan?'),
                                    'params' => [
                                        'InstallmentPlan[id]' => $model->id,
                                    ],
                                ],
                            ]
                        );
                    },
                    'restore' => function ($url, InstallmentPlan $model) {
                        return Html::a(
                            '<i class="fa fa-undo"></i>&nbsp;' . Yii::t('hipanel', 'Restore'),
                            $url,
                            [
                                'class' => 'btn btn-default btn-xs',
                                'data' => [
                                    'method' => 'POST',
                                    'pjax'   => '0',
                                    'params' => [
                                        'InstallmentPlan[id]' => $model->id,
                                    ],
                                ],
                            ]
                        );
                    },
                ],
                'urlCreator' => function (string $action, InstallmentPlan $model) {
                    return ['@installment-plan/' . $action, 'id' => $model->id];
                },
            ],
        ]);
    }

    public function tariffLink(InstallmentPlan $model): ?string
    {
        $canSeeLink = Yii::$app->user->can('plan.read');
        $tariff = Html::encode($model->tariff);

        return $canSeeLink ? Html::a($tariff, ['@plan/view', 'id' => $model->tariff_id]) : $tariff;
    }

    private function getChargeUrl(InstallmentPlan $model): ?array
    {
        if (!Yii::$app->user->can('bill.charges.read') || !$model->part_id) {
            return null;
        }

        return [
            '/finance/charge/index',
            'ChargeSearch' => [
                'object_id' => $model->part_id,
             ],
        ];
    }
}
