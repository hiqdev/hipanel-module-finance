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

    public $plan_type;

    public $template_plan_id;

    public $type;

    public $object_id;

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        $result = [
            [['plan_id', 'object_id', 'type'], 'required'],
            [['plan_id', 'object_id', 'template_plan_id'], 'integer'],
            [['type'], 'safe'],
        ];
        if ($this->plan_type !== Plan::TYPE_TEMPLATE) {
            $result[] = [['template_plan_id'], 'required'];
        }

        return $result;
    }

    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('hipanel.finance.price', 'Tariff plan'),
            'template_plan_id' => Yii::t('hipanel.finance.price', 'Template tariff plan'),
            'type' => Yii::t('hipanel', 'Type'),
            'object_id' => Yii::t('hipanel', 'Object'),
        ];
    }
}
