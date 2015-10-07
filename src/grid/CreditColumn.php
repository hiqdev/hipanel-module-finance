<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\controllers\BillController;

use Yii;
use hipanel\helpers\Url;

class CreditColumn
{
    public static function resolveConfig()
    {
        return Yii::$app->user->can('manage') ? [
            'class'          => 'hiqdev\xeditable\grid\XEditableColumn',
            'filter'         => false,
            'contentOptions' => ['class' => 'text-right'],
            'widgetOptions'  => [
                'class' => 'hiqdev\xeditable\widgets\RemoteFormatXEditable',
                'linkOptions' => [
                    'data-currency' => 'usd',
                ],
            ],
            'pluginOptions'  => [
                'type'               => 'remoteformat',
                'url'                => 'set-credit',
                'title'              => Yii::t('app', 'Set credit'),
                'ajaxUrl'            => Url::to('/format/currency'),
                'data-display-value' => function ($column, $options) {
                    return Yii::$app->formatter->format(array_shift($column->pluginOptions['value']), ['currency', 'USD']);
                },
                'ajaxDataOptions' => [
                    'currency' => 'currency'
                ],
            ]
        ] : [
            'class'          => 'hipanel\grid\CurrencyColumn',
            'contentOptions' => ['class' => 'text-right'],
        ];
    }
}
