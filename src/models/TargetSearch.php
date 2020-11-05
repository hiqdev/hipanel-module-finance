<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;

class TargetSearch extends Target
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }
}
