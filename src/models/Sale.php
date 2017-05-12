<?php

namespace hipanel\modules\finance\models;

use Yii;

class Sale extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    const SALE_TYPE_IP = 'ip';
    const SALE_TYPE_SERVER = 'server';
    const SALE_TYPE_ACCOUNT = 'account';
    const SALE_TYPE_CLIENT = 'client';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'buyer_id', 'seller_id', 'object_id', 'tariff_id'], 'integer'],
            [[
                'object',
                'object_like',
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
            self::SALE_TYPE_SERVER => Yii::t('hipanel:finance', 'Servers'),
            self::SALE_TYPE_IP => Yii::t('hipanel:finance', 'IP'),
            self::SALE_TYPE_ACCOUNT => Yii::t('hipanel:finance', 'Accounts'),
            self::SALE_TYPE_CLIENT => Yii::t('hipanel:finance', 'Clients'),
        ];
    }
}
