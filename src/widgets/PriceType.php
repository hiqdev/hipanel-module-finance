<?php

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\Type;

class PriceType extends Type
{
    public $values = [];
    public $defaultValues = [
        'success' => [
            'monthly,rack',
            'monthly,rack_unit',
            'monthly,ip_num',
            'monthly,support_time',
            'monthly,backup_du',
            'monthly,server_traf_max',
            'monthly,server_traf95_max',
            'monthly,server_du',
            'monthly,server_ssd',
            'monthly,server_sata',
            'monthly,win_license',
        ],
        'info' => [
            'overuse,rack_unit',
            'overuse,ip_num',
            'overuse,support_time',
            'overuse,backup_du',
            'overuse,server_traf_max',
            'overuse,server_traf95_max',
            'overuse,server_du',
            'overuse,server_ssd',
            'overuse,server_sata',
        ],
        'warning' => [
            'monthly,monthly',
        ],
        'default' => [
            'monthly,hardware',
            'monthly,leasing'
        ]
    ];
    public $field = 'type';
    public $i18nDictionary = 'hipanel.finance.priceTypes';

    /** {@inheritdoc} */
    protected function titlelize($label): string
    {
        return parent::titlelize(substr($label, strpos($label, ',')+1));
    }
}


