<?php

namespace hipanel\modules\finance\models;

use hiqdev\hiart\ActiveRecord;

class Value extends ActiveRecord
{
    public function attributes()
    {
        return [
            'currency',
            'value',
            'price',
            'discounted_price',
        ];
    }
}
