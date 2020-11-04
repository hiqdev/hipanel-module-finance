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
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['old_price', 'old_quantity'], 'number'],
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
            'old_quantity' => \Yii::t('hipanel.finance.price', 'Old quantity'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isQuantityPredefined(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        return new self([
            'time' => $row['time'],
            'old_price' => $row['old_price'],
            'old_quantity' => $row['old_quantity'],
        ]);
    }
}
