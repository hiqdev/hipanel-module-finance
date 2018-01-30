<?php

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\Type;

class PriceType extends Type
{
    public $values        = [];
    public $defaultValues = [
        'info'      => ['monthly'],
        'warning'   => ['server_traf_max'],
    ];
    public $field = 'type';
    public $i18nDictionary = 'hipanel.finance.priceTypes';
}
