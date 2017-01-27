<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use Yii;

/**
 * Class DomainResource.
 */
class DomainResource extends Resource
{
    use ModelTrait;

    public static function tableName()
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

    public function rules()
    {
        $rules = parent::rules();
        $rules['create-required'] = [
            ['object_id'],
            'required',
            'on' => ['create', 'update'],
            'when' => function ($model) {
                return $model->isTypeCorrect();
            },
        ];
        $rules['create-required-price'] = [['price'], 'required', 'on' => ['create', 'update']];
        $rules[] = [['zone'], 'safe'];

        return $rules;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return [
            static::TYPE_DOMAIN_REGISTRATION => Yii::t('hipanel:finance:tariff', 'Registration'),
            static::TYPE_DOMAIN_TRANSFER => Yii::t('hipanel:finance:tariff', 'Transfer'),
            static::TYPE_DOMAIN_RENEWAL => Yii::t('hipanel:finance:tariff', 'Renewal'),
            static::TYPE_DOMAIN_DELETE_AGP => Yii::t('hipanel:finance:tariff', 'Delete in AGP'),
            static::TYPE_DOMAIN_RESTORE_EXPIRED => Yii::t('hipanel:finance:tariff', 'Restoring expired'),
            static::TYPE_DOMAIN_RESTORE_DELETED => Yii::t('hipanel:finance:tariff', 'Restoring deleted'),
        ];
    }

    public function getServiceTypes()
    {
        return [
            static::TYPE_PREMIUM_DNS => Yii::t('hipanel:finance:tariff', 'Premium DNS'),
        ];
    }

    public function isTypeCorrect()
    {
        return isset($this->getTypes()[$this->type]);
    }
}
