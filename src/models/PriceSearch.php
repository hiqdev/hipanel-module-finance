<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use Yii;

class PriceSearch extends Price
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    protected function searchAttributes()
    {
        return array_merge(self::defaultSearchAttributes(), [
            'object_name_ilike'
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'currency_in' => Yii::t('hipanel', 'Currency'),
            'plan_name_ilike' => Yii::t('hipanel.finance.price', 'Plan'),
        ]);
    }
}
