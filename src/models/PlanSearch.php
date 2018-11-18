<?php

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use Yii;
use yii\helpers\ArrayHelper;

class PlanSearch extends Plan
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'states',
        ]);
    }
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type_in'             => Yii::t('hipanel', 'Type'),
            'name_ilike'          => Yii::t('hipanel:finance', 'Name'),
            'note_ilike'          => Yii::t('hipanel', 'Note'),
        ]);
    }
}
