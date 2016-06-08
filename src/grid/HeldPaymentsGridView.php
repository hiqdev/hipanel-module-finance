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

use Yii;
use hipanel\grid\ActionColumn;
use yii\helpers\Inflector;

class HeldPaymentsGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'user_comment' => [
                'attribute' => 'user_comment',
                'filterAttribute' => 'user_comment_like',
            ],
            'tech_comment' => [
                'attribute' => 'tech_comment',
            ],
            'payment_system' => [
                'header' => Yii::t('hipanel/finance', 'Payment system'),
                'value' => function ($model) {
                    return Inflector::titleize($model->params['system']);
                }
            ],
            'txn' => [
                'header' => Yii::t('hipanel/finance', 'TXN'),
                'value' => function ($model) {
                    return $model->params['txn'];
                }
            ],
            'label' => [
                'header' => Yii::t('hipanel', 'Description'),
                'value' => function ($model) {
                    return $model->params['label'];
                }
            ],
            'amount' => [
                'header' => Yii::t('hipanel/finance', 'Amount'),
                'format' => 'html',
                'value' => function ($model) {
                    $html = Yii::t('hipanel/finance/change', 'Full:') . "&nbsp;" . Yii::$app->formatter->asCurrency($model->params['sum'], $model->params['purse_currency']) . "<br />";
                    $html .= Yii::t('hipanel/finance/change', 'Fee:') . "&nbsp;" . Yii::$app->formatter->asCurrency($model->params['fee'], $model->params['purse_currency']) . "<br />";
                    $html .= Yii::t('hipanel/finance/change', 'Sum:') . "&nbsp;" . Yii::$app->formatter->asCurrency($model->params['sum'] - $model->params['fee'], $model->params['purse_currency']);
                    return $html;
                }
            ],
            'time' => [
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->time);
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
