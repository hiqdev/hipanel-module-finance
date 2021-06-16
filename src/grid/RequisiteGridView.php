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

use hipanel\grid\MainColumn;
use hipanel\helpers\Url;
use hipanel\modules\finance\menus\RequisiteActionsMenu;
use hipanel\modules\finance\models\Requisite;
use hiqdev\yii2\menus\grid\MenuColumn;
use hipanel\modules\client\grid\ContactGridView;
use hipanel\grid\XEditableColumn;
use hipanel\models\Ref;
use yii\helpers\Html;
use Yii;

class RequisiteGridView extends ContactGridView
{
    public $resizableColumns = false;

    public function columns()
    {
        $currencies = Ref::getList('type,currency');
        $formatter = Yii::$app->formatter;
        $cellLabels = [
            'balance' => Yii::t('hipanel:finance', "Balance"),
            'debit' => Yii::t('hipanel:finance', "Debit"),
            'credit' => Yii::t('hipanel:finance', "Credit"),
        ];
        $labelColors = [
            'debit' => '#ECFDF5',
            'credit' => '#FEF2F2',
        ];
        foreach (array_keys($currencies) as $currency) {
            $curColumns[$currency] = [
                'format' => 'html',
                'attribute' => 'balances',
                'filter' => false,
                'label' => Yii::t('hipanel:finance', strtoupper($currency)),
                'contentOptions' => [
                    'class' => 'no-padding',
                    'style' => 'width: 1%; white-space: nowrap;',
                ],
                'value' => function (Requisite $model) use ($currency, $cellLabels, $formatter, $labelColors): string {
                    $tags = [];
                    foreach ($cellLabels as $attribute => $label) {
                        $balance = $model->balances[$currency][$attribute] ?? null;
                        $color = $labelColors[$attribute] ?? null;
                        $tags[] = Html::tag('span', $formatter->asCurrency($balance, $currency), array_filter([
                            'title' => $label,
                            'style' => $color ? "background-color: $color;" : null,
                            'class' => 'text-right ' . ($attribute === 'balance' ? 'text-bold' : ''),
                        ]));
                    }
                    if (empty($tags)) {
                        return '';
                    }

                    return Html::tag('span', implode('', $tags), ['class' => 'balance-cell']);
                },
            ];
        }

        foreach ($cellLabels as $attribute => $label) {
            $balanceColumns[$attribute] = [
                'format' => 'raw',
                'attribute' => 'balance',
                'filter' => false,
                'label' => $label,
                'contentOptions' => [
                    'style' => "width: 1%; white-space: nowrap; background-color: $labelColors[$attribute]",
                    'class' => 'text-right ' . ($attribute === 'balance' ? 'text-bold' : ''),
                ],
                'value' => function (Requisite $model) use ($attribute, $formatter): string {
                    $balance = $model->balance[$attribute];
                    if (!empty($balance)) {
                        return Html::tag('span', $formatter->asCurrency($balance, $model->balance->currency ?? 'usd'));
                    }

                    return '';
                },
            ];
        }

        return array_merge(parent::columns(), [
            'name' => [
                'class' => MainColumn::class,
                'filterAttribute' => 'name_ilike',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id])
                        . "<br>"
                        . Html::encode($model->organization);
                }
                //'extraAttribute' => 'organization',
            ],
            'serie' => [
                'class' => XEditableColumn::class,
                'pluginOptions' => [
                    'url' => Url::to('@requisite/set-serie'),
                ],
                'contentOptions' => ['style' => 'width: 1%; white-space: nowrap;'],
                'filterOptions' => ['class' => 'narrow-filter'],
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => RequisiteActionsMenu::class,
                'contentOptions' => ['style' => 'width: 1%; white-space: nowrap;'],
            ],
        ], $curColumns ?? [],
            $balanceColumns ?? []
        );
    }
}
