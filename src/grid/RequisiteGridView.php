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
    public function columns()
    {
        $currencies = Ref::getList('type,currency');
        foreach ($currencies as $currency => $name) {
            $curColumns[$currency] = [
                'format' => 'raw',
                'attribute' => 'balances',
                'filter' => false,
                'label' => Yii::t('hipanel:finance', strtoupper($currency)),
                'value' => static function (Requisite $model) use ($currency): string {
                    $tags[] = Html::tag('span', Yii::t('hipanel:finance', "Current balance: {b}", [
                        'b' => $model->balances[$currency]['current_balance'] ?? "0.00",
                    ]), ['class' => 'label label-default']);
                    $tags[] = Html::tag('span', Yii::t('hipanel:finance', "Last balance: {b}", [
                        'b' => $model->balances[$currency]['last_balance'] ?? "0.00",
                    ]), ['class' => 'label label-primary']);
                    $tags[] = Html::tag('span', Yii::t('hipanel:finance', "Start balance: {b}", [
                        'b' => $model->balances[$currency]['previous_balance'] ?? "0.00",
                    ]), ['class' => 'label label-success']);
                    $tags[] = Html::tag('span', Yii::t('hipanel:finance', "Deposit: {b}", [
                        'b' => $model->balances[$currency]['deposit'] ?? "0.00",
                    ]), ['class' => 'label label-info']);
                    $tags[] = Html::tag('span', Yii::t('hipanel:finance', "Withdraw: {b}", [
                        'b' => $model->balances[$currency]['withdraw'] ?? "0.00",
                    ]), ['class' => 'label label-danger']);
                    $tags[] = Html::tag('span', Yii::t('hipanel:finance', "Saldo: {b}", [
                        'b' => $model->balances[$currency]['saldo'] ?? "0.00",
                    ]), ['class' => 'label label-success']);
                    return implode("<br/>", $tags);
                },
            ];
        }

        foreach ([
                'current_balance' => Yii::t('hipanel:finance', "Current balance"),
                'last_balance' => Yii::t('hipanel:finance', "Last balance"),
                'previous_balance' => Yii::t('hipanel:finance', "Start balance"),
                'deposit' => Yii::t('hipanel:finance', "Deposit"),
                'withdraw' => Yii::t('hipanel:finance', "Withdraw"),
                'saldo' => Yii::t('hipanel:finance', "Saldo"),
        ] as $attr => $name) {
            $balanceColumns[$attr] = [
                'format' => 'raw',
                'attribute' => 'balance',
                'filter' => false,
                'label' => $name,
                'value' => static function (Requisite $model) use ($attr): string {
                    return Html::tag('span', $model->balance[$attr] ?? "0.00");
                },
            ];
        }

        return array_merge(parent::columns(), [
            'serie' => [
                'class' => XEditableColumn::class,
                'pluginOptions' => [
                    'url' => Url::to('@requisite/set-serie'),
                ],
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => RequisiteActionsMenu::class,
            ],
            ], $curColumns ?? [],
            $balanceColumns ?? []
        );
    }
}
