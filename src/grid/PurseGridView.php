<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
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
                    return ArraySpoiler::widget([
                        'mode'              => ArraySpoiler::MODE_SPOILER,
                        'data'              => $model->files,
                        'delimiter'         => ' ',
                        'formatter'         => function ($file, $index) {
                            return self::pdfLink($file, $file['month']);
                        },
                        'template'          => '{button}{visible}{hidden}',
                        'visibleCount'      => 2,
                        'button'            => [
                            'label' => FontIcon::i('fa-history fa-2x') . ' ' . Yii::t('app', 'Archive'),
                            'class' => 'pull-right text-nowrap',
                        ],
                    ]);
                },
            ],
        ];
    }

    public static function pdfLink($file, $month = 'now')
    {
        return Html::a(FontIcon::i('fa-file-pdf-o fa-2x') . date(' M Y', strtotime($month)), "/file/$file[id]/$file[filename]", ['target' => '_blank', 'class' => 'text-info text-nowrap col-xs-6 col-sm-6 col-md-6 col-lg-3']);
    }
}
