<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use Yii;

class DomainServicePrice extends Price
{
    const SERVICE_OPERATION_PURCHASE = 'feature,premium_dns_purchase';
    const SERVICE_OPERATION_RENEW = 'feature,premium_dns_renew';

    public static function tableName()
    {
        return 'price';
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['create-required-price'] = [['price'], 'required'];
        $rules[] = [['price'], 'number', 'min' => 0];

        return $rules;
    }

    /**
     * @return array available operations
     */
    public static function getOperations(): array
    {
        return [
            static::SERVICE_OPERATION_PURCHASE => Yii::t('hipanel:finance:tariff', 'Purchase'),
            static::SERVICE_OPERATION_RENEW => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }

    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return Yii::t('hipanel:finance:tariff', 'Premium DNS');
    }
}
