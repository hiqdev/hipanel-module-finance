<?php

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\Type;

class BillType extends Type
{
    public $defaultValues = [
        'success' => [
            'deposit,*',
            'monthly,*',
        ],
        'info' => [
            'overuse,*',
        ],
        'warning' => [
            'monthly,monthly',
            'exchange,*'
        ],
        'primary' => [
            'monthly,leasing',
        ],
        'default' => [
            'monthly,hardware',
            'correction,*',
        ],
    ];
    public $field = 'type';
    public $i18nDictionary = 'hipanel:finance';

    /** {@inheritdoc} */
    protected function titlelize($label): string
    {
        return parent::titlelize(substr($label, strpos($label, ',')+1));
    }
}


