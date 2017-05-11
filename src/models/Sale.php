<?php

namespace hipanel\modules\finance\models;

use Yii;

class Sale extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'buyer_id', 'seller_id', 'object_id', 'tariff_id'], 'integer'],
            [[
                'object',
                'seller',
                'login',
                'buyer',
                'tariff',
                'time',
                'expires',
                'renewed_num',
                'sub_factor',
                'tariff_type',
                'is_grouping',
                'from_old',
            ], 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'buyer' => Yii::t('hipanel:finance', 'Buyer'),
            'seller' => Yii::t('hipanel:finance', 'Seller'),
        ]);
    }

    public function getTypes()
    {
        return [
            'server' => Yii::t('hipanel:finance', 'Servers'),
            'ip' => Yii::t('hipanel:finance', 'IP'),
            'account' => Yii::t('hipanel:finance', 'Accounts'),
            'client' => Yii::t('hipanel:finance', 'Clients'),
        ];
    }
}
