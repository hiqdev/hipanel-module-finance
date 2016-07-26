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

use hipanel\grid\CurrencyColumn;
use hipanel\grid\XEditableColumn;
use hipanel\helpers\Url;
use hiqdev\xeditable\widgets\RemoteFormatXEditable;
use Yii;

class CreditColumn
{
    public static function resolveConfig()
    {
        return Yii::$app->user->can('manage') ? [
            'class'          => XEditableColumn::class,
            'filter'         => false,
            'contentOptions' => ['class' => 'text-right'],
            'widgetOptions'  => [
                'class' => RemoteFormatXEditable::class,
                'linkOptions' => [
                    'data-currency' => 'usd',
                ],
            ],
            'pluginOptions'  => [
                'url'                => '@client/set-credit',
                'title'              => Yii::t('app', 'Set credit'),
                'ajaxUrl'            => Url::to('/format/currency'),
                'data-display-value' => function ($column, $options) {
                    return Yii::$app->formatter->format(array_shift($column->pluginOptions['value']), ['currency', 'USD']);
                },
                'ajaxDataOptions' => [
                    'currency' => 'currency',
                ],
            ],
        ] : [
            'class'          => CurrencyColumn::class,
            'contentOptions' => ['class' => 'text-right'],
        ];
    }
}
