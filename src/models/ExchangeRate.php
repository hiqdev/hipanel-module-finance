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
use hiqdev\hiart\ActiveQuery;
use hiqdev\hiart\ActiveRecord;

/**
 * Class ExchangeRate.
 *
 * @property string $from
 * @property string $to
 * @property float $rate
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ExchangeRate extends ActiveRecord
{
    use ModelTrait;

    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     * @return ActiveQuery
     */
    public static function find()
    {
        return parent::find()->action('search-rates');
    }

    public function rules()
    {
        return [
            [['from', 'to', 'rate'], 'safe'],
        ];
    }

    public function pairCode(string $delimiter = '/'): string
    {
        return implode($delimiter, [$this->from, $this->to]);
    }

    public function attributeLabels()
    {
        return [
            'from' => \Yii::t('hipanel:finance', 'From'),
            'to' => \Yii::t('hipanel:finance', 'To'),
            'rate' => \Yii::t('hipanel:finance', 'Rate'),
        ];
    }
}
