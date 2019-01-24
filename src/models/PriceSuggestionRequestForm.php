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
    public const SCENARIO_PREDEFINED_OBJECT = 'predefinedObject';

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
            [['plan_id', 'type'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_PREDEFINED_OBJECT]],
            [['object_id', 'type'], 'required', 'on' => [self::SCENARIO_DEFAULT]],
            [['plan_id', 'object_id', 'template_plan_id'], 'integer', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_PREDEFINED_OBJECT]],
            [['type'], 'safe', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_PREDEFINED_OBJECT]],
        ];
        if ($this->plan_type !== Plan::TYPE_TEMPLATE) {
            $result[] = [['template_plan_id'], 'required'];
        }

        return $result;
    }

    public function isObjectPredefined(): bool
    {
        return $this->scenario === self::SCENARIO_PREDEFINED_OBJECT;
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
