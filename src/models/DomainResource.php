<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use Yii;

/**
 * Class DomainResource
 * @package hipanel\modules\finance\models
 */
class DomainResource extends Resource
{
    public static function index()
    {
        return 'resources';
    }

    public static function type()
    {
        return 'resource';
    }

    const TYPE_DOMAIN_REGISTRATION = 'dregistration';
    const TYPE_DOMAIN_TRANSFER = 'dtransfer';
    const TYPE_DOMAIN_RENEWAL = 'drenewal';
    const TYPE_DOMAIN_DELETE_AGP = 'ddelete_agp';
    const TYPE_DOMAIN_RESTORE_EXPIRED = 'drestore_expired';
    const TYPE_DOMAIN_RESTORE_DELETED = 'drestore_deleted';

    const TYPE_PREMIUM_DNS = 'premium_dns';

    /**
     * @return array
     */
    public function getAvailableTypes()
    {
        return [
            static::TYPE_DOMAIN_REGISTRATION => Yii::t('hipanel/finance/tariff', 'Registration'),
            static::TYPE_DOMAIN_TRANSFER => Yii::t('hipanel/finance/tariff', 'Transfer'),
            static::TYPE_DOMAIN_RENEWAL => Yii::t('hipanel/finance/tariff', 'Renewal'),
            static::TYPE_DOMAIN_DELETE_AGP => Yii::t('hipanel/finance/tariff', 'Delete in AGP'),
            static::TYPE_DOMAIN_RESTORE_EXPIRED => Yii::t('hipanel/finance/tariff', 'Restoring expired'),
            static::TYPE_DOMAIN_RESTORE_DELETED => Yii::t('hipanel/finance/tariff', 'Restoring deleted'),
        ];
    }

    public function getServiceTypes()
    {
        return [
            static::TYPE_PREMIUM_DNS => Yii::t('hipanel/finance/tariff', 'Premium DNS')
        ];
    }


    public function isTypeCorrect()
    {
        return isset($this->getAvailableTypes()[$this->type]);
    }
}
