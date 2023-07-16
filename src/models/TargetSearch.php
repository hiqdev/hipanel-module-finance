<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use yii\helpers\ArrayHelper;

class TargetSearch extends Target
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), ['tags']);
    }
}
