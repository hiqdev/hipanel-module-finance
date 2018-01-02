<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;

class PlanSearch extends Plan
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }
}
