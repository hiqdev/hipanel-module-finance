<?php

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use Yii;

class OveruseServerPrice extends Price
{
    use ModelTrait;

    public static function tableName()
    {
        return 'price';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['count_aggregated_traffic'], 'boolean', 'trueValue' => 1, 'falseValue' => 0];

        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'count_aggregated_traffic' => Yii::t('hipanel.finance.price', 'Calculate aggregated traffic'),
        ]);
    }

    /** {@inheritdoc} */
    public function isServer95Traf()
    {
        return strpos($this->type, 'overuse,server_traf95_max') === 0;
    }

}
