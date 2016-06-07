<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\ActionColumn;
use Yii;

class ChangeGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'user_comment' => [
                'attribute' => 'user_comment',
            ],
            'tech_comment' => [
                'attribute' => 'tech_comment',
            ],
            'time' => [
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->time);
                }
            ],
            'tech_details' => [
                'label' => Yii::t('hipanel/finance/change', 'Operation details'),
                'value' => function ($model) {
                    return null;
                }
            ],
            'actions' => [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'header' => Yii::t('hipanel', 'Actions'),
            ],
        ];
    }
}
