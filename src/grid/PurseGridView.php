<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (https://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\helpers\FontIcon;
use Yii;
use yii\helpers\Html;

class PurseGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'balance' => [
                'class' => 'hipanel\modules\finance\grid\BalanceColumn',
            ],
            'credit' => CreditColumn::resolveConfig(),
            'invoices' => [
                'format' => 'raw',
                'value'  => function ($model) {
                    $curr = date('Y-m-1');
                    $prev = date('Y-m-1', strtotime($curr) - 1000);
                    return Html::a(Yii::t('app', 'Archive'), ['@purse/invoice-archive', 'id' => $model->id], ['class' => 'btn btn-default btn-xs pull-right']).
                        self::pdfLink($model, $curr) . ' &nbsp; ' . self::pdfLink($model, $prev);
                }
            ],
        ];
    }

    public static function pdfLink($model, $month = 'now')
    {
        $date = strtotime($month);
        return Html::a(FontIcon::i('fa-file-pdf-o fa-2x') . date(' M Y', $date), ['@purse/pdf-invoice', 'id' => $model->id, 'month' => date('Y-m-01', $date)], ['target' => '_blank']);
    }
}
