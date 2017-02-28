<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\widgets\ArraySpoiler;
use hipanel\helpers\FontIcon;
use Yii;
use yii\helpers\Html;

class DocumentsColumn extends \hipanel\grid\DataColumn
{
    public $format = 'raw';

    public function getDataCellValue($model, $key, $index)
    {
        return ArraySpoiler::widget([
            'mode'              => ArraySpoiler::MODE_SPOILER,
            'data'              => parent::getDataCellValue($model, $key, $index),
            'delimiter'         => ' ',
            'formatter'         => function ($doc) {
                return Html::a(
                    FontIcon::i('fa-file-pdf-o fa-2x') . date(' M Y', strtotime($doc->validity_start)),
                    ["/file/{$doc->file_id}/{$doc->filename}", 'nocache' => 1],
                    ['target' => '_blank', 'class' => 'text-info text-nowrap col-xs-6 col-sm-6 col-md-6 col-lg-3']
                );
            },
            'template'          => '{button}{visible}{hidden}',
            'visibleCount'      => 2,
            'button'            => [
                'label' => FontIcon::i('fa-history fa-2x') . ' ' . Yii::t('hipanel', 'History'),
                'class' => 'pull-right text-nowrap',
            ],
        ]);
    }
}
