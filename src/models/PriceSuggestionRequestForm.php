<?php

namespace hipanel\modules\finance\models;

use Yii;
use yii\base\Model;

/**
 * Class PriceSuggestionRequestForm
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceSuggestionRequestForm extends Model
{
    public $plan_id;

    public $type;

    public $object_id;

    public function rules()
    {
        return [
            [['plan_id', 'object_id', 'type'], 'required'],
            [['plan_id', 'object_id'], 'integer'],
            [['type'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('hipanel.finance.price', 'Plan'),
            'type' => Yii::t('hipanel', 'Type'),
            'object_id' => Yii::t('hipanel', 'Object'),
        ];
    }
}
