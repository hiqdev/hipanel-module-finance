<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use yii\helpers\ArrayHelper;

class TargetSearch extends Target
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    public static function tableName()
    {
        return Target::tableName();
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), ['tags']);
    }
}
