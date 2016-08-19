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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tariff_id', 'object_id', 'type_id', 'unit_id', 'currency_id', 'hardlim'], 'integer'],
            [['type', 'ftype', 'unit', 'unit_factor', 'currency'], 'safe'],
            [['price', 'fee', 'quantity', 'discount'], 'number'],

            [['object_id', 'type_id'], 'integer', 'on' => 'create'],
            [['type'], 'safe', 'on' => 'create'],
            [['price'], 'number', 'on' => 'create'],
            'create-required' => [['object_id', 'price'], 'required', 'on' => 'create'],
        ];
    }

    public function getTariff()
    {
        return $this->hasOne(Tariff::class, ['tariff_id' => 'id']);
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
