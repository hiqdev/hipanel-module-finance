<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use yii\helpers\ArrayHelper;
use Yii;

class PnlSearch extends Pnl
{
    use SearchModelTrait {
        SearchModelTrait::searchAttributes as defaultSearchAttributes;
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'ids',
            'has_no_type',
            'has_note',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type_id' => Yii::t('hipanel', 'Type'),
            'type' => Yii::t('hipanel', 'Type'),
            'currency_id' => Yii::t('hipanel:finance', 'Currency'),
            'currency' => Yii::t('hipanel:finance', 'Currency'),
        ]);
    }
}
