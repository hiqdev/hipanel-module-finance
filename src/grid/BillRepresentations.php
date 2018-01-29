<?php

namespace hipanel\modules\finance\grid;

use hiqdev\higrid\representations\RepresentationCollection;
use Yii;

class BillRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'common'),
                'columns' => [
                    'checkbox',
                    'actions',
                    'client_id',
                    'time',
                    'sum_editable',
                    'balance',
                    'type_label',
                    'description',
                ],
            ],
        ]);
    }
}
