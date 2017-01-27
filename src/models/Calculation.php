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

use Yii;
use yii\base\InvalidParamException;

/**
 * Class Calculation.
 */
class Calculation extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /** {@inheritdoc} */
    public static function tableName()
    {
        return 'action';
    }

    /** {@inheritdoc} */
    public function init()
    {
        if (!isset($this->seller)) {
            if (Yii::$app->user->getIsGuest()) {
                $this->seller = Yii::$app->params['user.seller'];
            } else {
                $this->seller = Yii::$app->user->identity->seller;
            }
        }

        if (!isset($this->client) && !Yii::$app->user->getIsGuest()) {
            $this->client = Yii::$app->user->identity->username;
        }

        $this->synchronize();
    }

    public function getValue()
    {
        // ['tariff_id' => 'currency'] is a dumb relation condition and does not make any sense
        return $this->hasMany(Value::class, ['tariff_id' => 'currency'])->indexBy('currency');
    }

    /**
     * @param string $currency
     *
     * @throws InvalidParamException when the $currency is not calculated
     * @return Value
     */
    public function forCurrency($currency)
    {
        if (!isset($this->value[$currency])) {
            Yii::warning('Value for currency $currency was not found. Using fake free value. Most probably, tariff is free', __METHOD__);
            return new Value(['value' => 0, 'price' => 0]);
        }

        return $this->value[$currency];
    }

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['object', 'seller', 'client', 'type', 'currency', 'item'], 'safe'],
            [['amount'], 'number'],
            [['tariff_id', 'calculation_id'], 'integer'],
        ];
    }

    /**
     * Synchronises the model to represent actual state of [[position]]
     * The method must update values, that affects the calculation and
     * can be changed in cart without position re-adding.
     * For example: quantity.
     */
    public function synchronize()
    {
        return true;
    }
}
