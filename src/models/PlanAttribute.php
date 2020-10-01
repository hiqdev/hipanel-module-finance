<?php

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;
use Yii;

/**
 * @property string|null name
 * @property string|null value
 */
class PlanAttribute extends Model
{
    use ModelTrait;

    public function rules()
    {
        return [
            [['name', 'value'], 'string'],
            [['name', 'value'], 'trim'],
        ];
    }

    public function isEmpty(): bool
    {
        return empty($this->name) || empty($this->value);
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('hipanel:finance', 'Name'),
            'value' => Yii::t('hipanel:finance', 'Value'),
        ];
    }
}