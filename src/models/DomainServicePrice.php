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
    const FEATURE_PREMIUM_DNS_PURCHASE   = 'feature,premium_dns_purchase';
    const FEATURE_PREMIUM_DNS_RENEW      = 'feature,premium_dns_renew';
    const FEATURE_WHOIS_PROTECT_PURCHASE = 'feature,whois_protect_purchase';
    const FEATURE_WHOIS_PROTECT_RENEW    = 'feature,whois_protect_renew';

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
            static::FEATURE_PREMIUM_DNS_PURCHASE => Yii::t('hipanel:finance:tariff', 'Purchase'),
            static::FEATURE_PREMIUM_DNS_RENEW => Yii::t('hipanel:finance:tariff', 'Renewal'),
            static::FEATURE_WHOIS_PROTECT_PURCHASE => Yii::t('hipanel:finance:tariff', 'Purchase'),
            static::FEATURE_WHOIS_PROTECT_RENEW => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }

    /**
     * @return string|null
     */
    public static function getLabel(string $labelType): ?string
    {
        $variants = [
            'premium_dns' => Yii::t('hipanel:finance:tariff', 'Premium DNS'),
            'whois_protect' => Yii::t('hipanel:finance:tariff', 'WHOIS Protect'),
        ];

        return $variants[$labelType];
    }
}
