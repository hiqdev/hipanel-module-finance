<?php

namespace hipanel\modules\finance\models;

use Yii;

/**
 * Class Sale
 *
 * @property string|int $id
 * @property string|int $object_id
 * @property string|int $tariff_id
 * @property string $tariff_type
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Sale extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    const SALE_TYPE_IP = 'ip';
    const SALE_TYPE_SERVER = 'server';
    const SALE_TYPE_PCDN = 'pcdn';
    const SALE_TYPE_VCDN = 'vcdn';
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
            'time' => Yii::t('hipanel:finance:sale', 'Time'),
            'object' => Yii::t('hipanel:finance:sale', 'Object'),
            'object_like' => Yii::t('hipanel:finance:sale', 'Object'),
            'buyer' => Yii::t('hipanel:finance:sale', 'Buyer'),
            'buyer_id' => Yii::t('hipanel:finance:sale', 'Buyer'),
            'seller' => Yii::t('hipanel:finance:sale', 'Seller'),
            'seller_id' => Yii::t('hipanel:finance:sale', 'Seller'),
            'tariff_id' => Yii::t('hipanel:finance', 'Tariff'),
            'tariff_type' => Yii::t('hipanel', 'Type'),
        ]);
    }

    public function getTypes()
    {
        return [
            self::SALE_TYPE_SERVER => Yii::t('hipanel:finance', 'Servers'),
            self::SALE_TYPE_IP => 'IP',
            self::SALE_TYPE_ACCOUNT => Yii::t('hipanel:hosting', 'Accounts'),
            self::SALE_TYPE_CLIENT => Yii::t('hipanel', 'Clients'),
        ];
    }

}
