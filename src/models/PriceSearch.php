<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;

class PriceSearch extends Price
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }
}
