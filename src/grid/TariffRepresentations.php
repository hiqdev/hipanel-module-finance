<?php

namespace hipanel\modules\finance\grid;

use hiqdev\higrid\representations\RepresentationCollection;
use Yii;

class TariffRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $columns = Yii::$app->user->can('manage') ? [
            'checkbox',
            'tariff',
            'used',
            'type',
            'client_id',
            'seller_id',
        ] : [
            'tariff',
            'used',
            'client_id',
            'seller_id',
        ];
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'common'),
                'columns' => $columns,
            ],
        ]);
    }
}
