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

use hipanel\base\ModelTrait;

/**
 * Class PlanHistory
 *
 * @property \DateTime $time
 * @property float $old_price
 */
class PriceHistory extends Price
{
    use ModelTrait;

    /**
     * @var Price
     */
    private $_price;

    /**
     * PriceHistory constructor.
     * @param Price $price
     * @param array $config
     */
    public function __construct(Price $price, $config = [])
    {
        parent::__construct($config);
        $this->_price = $price;
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (\Throwable $e) {
            return $this->_price->$name;
        }
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['old_price'], 'number'],
            [['time'], 'date'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'old_price' => \Yii::t('hipanel.finance.price', 'Old price'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'price-history';
    }

    /**
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        $price = parent::instantiate($row);

        return new self($price, [
            'time' => $row['time'],
            'old_price' => $row['old_price'],
        ]);
    }
}
