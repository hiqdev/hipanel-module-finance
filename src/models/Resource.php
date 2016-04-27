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

class Resource extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    const TYPE_DOMAIN_REGISTRATION = 'dregistration';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'id',
                'tariff_id',
                'object_id',
                'type_id',
                'unit_id',
                'currency_id',
                'type',
                'ftype',
                'unit',
                'unit_factor',
                'currency',
                'price',
                'fee',
                'quantity',
                'hardlim',
                'discount',
                'zone',
            ], 'safe'],
        ];
    }

    public function getTariff()
    {
        return $this->hasOne(Tariff::className(), ['tariff_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
        ]);
    }
}
