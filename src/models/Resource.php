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

use hipanel\modules\stock\models\Part;
use Yii;
use yii\base\InvalidConfigException;

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

            [['object_id', 'type_id'], 'integer', 'on' => ['create', 'update']],
            [['type'], 'safe', 'on' => ['create', 'update']],
            [['price'], 'number', 'on' => ['create', 'update']],
            'create-required' => [['object_id', 'price'], 'required', 'on' => ['create', 'update']],
        ];
    }

    public function getTariff()
    {
        return $this->hasOne(Tariff::class, ['tariff_id' => 'id']);
    }

    public function getPart()
    {
        if (!Yii::getAlias('@part', false)) {
            throw new InvalidConfigException('Stock module is a must to retrieve resource parts');
        }

        return $this->hasOne(Part::class, ['object_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
        ]);
    }

    public function isTypeCorrect()
    {
        return isset($this->getAvailableTypes()[$this->type]);
    }

    public function getAvailableTypes()
    {
        return [];
    }
}
