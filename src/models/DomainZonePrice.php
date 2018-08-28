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
 * Class DomainZonePrice.
 */
class DomainZonePrice extends Price
{
    use ModelTrait;

    public static function tableName()
    {
        return 'price';
    }

    public const TYPE_DOMAIN_REGISTRATION = 'domain,dregistration';
    public const TYPE_DOMAIN_TRANSFER = 'domain,dtransfer';
    public const TYPE_DOMAIN_RENEWAL = 'domain,drenewal';
    public const TYPE_DOMAIN_DELETE_AGP = 'domain,ddelete_agp';
    public const TYPE_DOMAIN_RESTORE_EXPIRED = 'domain,drestore_expired';
    public const TYPE_DOMAIN_RESTORE_DELETED = 'domain,drestore_deleted';

    public function rules()
    {
        $rules = parent::rules();
        $rules['create-required'] = [
            ['object_id'],
            'required',
            'on' => ['create', 'update'],
            'when' => function ($model) {
                /** @var DomainZonePrice $model */
                return $model->isTypeCorrect();
            },
        ];
        $rules['create-required-price'] = [['price'], 'required'];
        $rules[] = [['price'], 'number', 'min' => 0];

        return $rules;
    }

    /**
     * @return bool
     */
    public function isTypeCorrect(): bool
    {
        return isset($this->getTypes()[$this->type]);
    }

    /**
     * @return array
     */
    public static function getTypes(): array
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
}
