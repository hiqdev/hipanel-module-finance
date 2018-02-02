<?php

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\Type;

class PriceType extends Type
{
    public $values        = [];
    public $defaultValues = [
        'success'   => ['monthly'],
        'warning'   => ['ip_num', 'server_traf_max', 'server_traf95_max'],
        'info'      => ['support_time', 'backup_du'],
        'danger'    => ['db_num', 'domain_num', 'domain_traf'],
    ];
    public $field = 'type';
    public $i18nDictionary = 'hipanel.finance.priceTypes';
}
