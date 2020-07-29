<?php

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use Yii;

class RatePrice extends Price
{
    use ModelTrait;

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['rate'], 'number'];

        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'rate' => Yii::t('hipanel.finance.price', 'Referral rate'),
        ]);
    }
}
