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
use hipanel\widgets\ArraySpoiler;
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
                    return Html::a(FontIcon::i('fa-history fa-2x') . ' ' .Yii::t('app', 'Archive'), ['@purse/invoice-archive', 'id' => $model->id], ['class' => 'pull-right text-nowrap']).
                     ArraySpoiler::widget([
                        'data'              => $model->files,
                        'formatter'         => function ($file) {
                            return self::pdfLink($file, $file['month']);
                        },
                        'visibleCount'      => 2,
                        'button'    => [
                            'clientOptions' => ['html' => true],
                        ]
                    ]);
                }
            ],
        ];
    }

    public static function pdfLink($file, $month = 'now')
    {
        return Html::a(FontIcon::i('fa-file-pdf-o fa-2x') . date(' M Y', strtotime($month)), "/file/$file[id]/$file[filename]", ['target' => '_blank', 'class' => 'text-info text-nowrap']);
    }
}
