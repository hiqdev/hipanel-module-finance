<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\DataColumn;
use hipanel\grid\MainColumn;
use hipanel\grid\RefColumn;
use hipanel\grid\CurrencyColumn;
use hipanel\helpers\Url;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\menus\PlanActionsMenu;
use hipanel\modules\finance\models\Plan;
use hipanel\widgets\CustomAttributesViewer;
use hipanel\widgets\IconStateLabel;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\helpers\Html;

class PlanGridView extends \hipanel\grid\BoxedGridView
{
    public function columns(): array
    {
        return array_merge(parent::columns(), [
            'client' => [
                'class' => ClientColumn::class,
            ],
            'name' => [
                'attribute' => 'name',
                'filterAttribute' => 'name_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'class' => MainColumn::class,
                'note' => 'note',
                'noteOptions' => [
                    'url' => Url::to(['@plan/set-note']),
                ],
                'badges' => function (Plan $model): string {
                    return $this->prepareBadges($model);
                },
            ],
            'simple_name' => [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (Plan $model): string {
                    return sprintf('%s %s', Html::encode($model->name), $this->prepareBadges($model));
                },
            ],
            'state' => [
                'attribute' => 'state',
                'class' => RefColumn::class,
                'filterAttribute' => 'state',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'raw',
                'gtype' => 'state,tariff',
            ],
            'type' => [
                'attribute' => 'type',
                'class' => RefColumn::class,
                'filterAttribute' => 'type_in',
                'filterOptions' => ['class' => 'narrow-filter'],
                'i18nDictionary' => 'hipanel.finance.suggestionTypes',
                'format' => 'raw',
                'gtype' => 'type,tariff',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => PlanActionsMenu::class,
            ],
            'monthly' => [
                'attribute' => 'monthly',
                'contentOptions' => ['id' => 'plan-monthly-value'],
            ],
            'custom_attributes' => [
                'class' => DataColumn::class,
                'label' => Yii::t('hipanel:finance', 'Attributes'),
                'format' => 'raw',
                'contentOptions' => ['style' => 'padding: 0;'],
                'value' => static fn(Plan $plan): string => CustomAttributesViewer::widget(['owner' => $plan]),
            ],
            'fee' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'fee',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function (Plan $model) {
                    return ['class' => 'text-right'];
                },
            ],
            'is_sold' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel:finance', 'Is sold'),
                'filter' => false,
                'headerOptions' => ['class' => 'narrow-filter text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => fn(Plan $model): string => IconStateLabel::widget([
                    'model' => $model,
                    'attribute' => 'is_sold',
                    'icons' => ['fa-check-circle', 'fa-times-circle'],
                    'colors' => ['#00a65a', '#d73925'],
                    'messages' => [
                        Yii::t('hipanel:finance', 'Plan is sold'),
                        Yii::t('hipanel:finance', 'Plan is not sold'),
                    ],  
                ]),
            ],
        ]);
    }

    /**
     * @param Plan $model
     * @return string
     */
    protected function prepareBadges(Plan $model): string
    {
        $html = '';
        if ($model->your_tariff) {
            $html .=  Html::tag('span', Html::tag('i', null, ['class' => 'fa fa-lock']), [
                'class' => 'label bg-red pull-right',
                'style' => 'margin-left: .1em',
                'title' => Yii::t('hipanel.finance.plan', 'Your tariff plan')
            ]);
        }
        if ($model->is_grouping) {
            $localization = Yii::t('hipanel.finance.plan', 'Grouping');
            $html .= Html::tag('span', $localization, ['class' => 'label bg-olive pull-right']);
        }

        return $html;
    }
}
