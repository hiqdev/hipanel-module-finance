<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;
use Yii;

class Pnl extends Model
{
    use ModelTrait;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['id', 'charge_id', 'type_id', 'currency_id', 'sum', 'charge_sum', 'discount_sum'], 'integer'],
            [['type', 'currency'], 'string'],
            [['update_time', 'month'], 'date'],
            [['data'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('hipanel:finance', 'Tariff plan'),
            'plan' => Yii::t('hipanel:finance', 'Tariff plan'),
            'quantity' => Yii::t('hipanel:finance', 'Prepaid'),
            'unit' => Yii::t('hipanel:finance', 'Unit'),
            'price' => Yii::t('hipanel:finance', 'Price'),
            'formula' => Yii::t('hipanel.finance.price', 'Formula'),
            'note' => Yii::t('hipanel', 'Note'),
            'type' => Yii::t('hipanel', 'Type'),
        ];
    }
}
