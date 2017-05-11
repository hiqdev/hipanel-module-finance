<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;

class SaleSearch extends Sale
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }
}
