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

use hipanel\modules\finance\models\query\TariffQuery;
use Yii;

/**
 * Class Tariff
 * @package hipanel\modules\finance\models
 * @property Resource[]|DomainResource[]|ServerResource[] $resources
 */
class Tariff extends \hipanel\base\Model implements CalculableModelInterface
{
    use \hipanel\base\ModelTrait;

    const TYPE_DOMAIN = 'domain';
    const TYPE_XEN = 'svds';
    const TYPE_OPENVZ = 'ovds';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'seller_id', 'id', 'parent_id'], 'integer'],
            [['client', 'seller', 'bill', 'name'], 'safe'],
            [['domain', 'server'], 'safe'],
            [['tariff', 'tariff_id'], 'safe'],
            [['type_id', 'state_id'], 'integer'],
            [['type', 'state', 'currency'], 'safe'],
            [['used'], 'integer'],
            [['note', 'label'], 'safe'],
            [['is_personal'], 'boolean'],
            [['id'], 'required', 'on' => ['delete']],
        ];
    }

    public function getResources()
    {
        if ($this->type === self::TYPE_DOMAIN) {
            return $this->hasMany(DomainResource::class, ['tariff_id' => 'id'])->inverseOf('tariff');
        } elseif (in_array($this->type, [self::TYPE_XEN, self::TYPE_OPENVZ])) {
            return $this->hasMany(ServerResource::class, ['tariff_id' => 'id'])->inverseOf('tariff');
        }

        return $this->hasMany(Resource::class, ['tariff_id' => 'id'])->inverseOf('tariff');
    }

    /**
     * @param $type
     * @return DomainResource|ServerResource|Resource
     */
    public function getResourceByType($type)
    {
        foreach ($this->resources as $resource) {
            if ($resource->type === $type) {
                return $resource;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'used' => Yii::t('hipanel:finance:tariff', 'Used'),
        ]);
    }

    /**
     * {@inheritdoc}
     * @return TariffQuery
     */
    public static function find($options = [])
    {
        return new TariffQuery(get_called_class(), [
            'options' => $options,
        ]);
    }

    public function getGeneralType()
    {
        if ($this->type === static::TYPE_DOMAIN) {
            return 'domain';
        } elseif (in_array($this->type, [static::TYPE_OPENVZ, static::TYPE_XEN])) {
            return 'server';
        }

        return null;
    }

    /**
     * Method creates and returns corresponding Calculation model.
     *
     * @return Calculation
     */
    public function getCalculationModel()
    {
        return new Calculation([
            'calculation_id' => $this->id,
            'tariff_id' => $this->id,
            'object' => $this->getGeneralType()
        ]);
    }
}
