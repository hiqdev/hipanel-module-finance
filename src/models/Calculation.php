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

use Guzzle\Plugin\ErrorResponse\Exception\ErrorResponseException;
use Yii;
use yii\base\InvalidParamException;

/**
 * Class Calculation.
 */
class Calculation extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /** {@inheritdoc} */
    public static function index()
    {
        return 'actions';
    }

    /** {@inheritdoc} */
    public static function type()
    {
        return 'action';
    }

    /** {@inheritdoc} */
    public static function primaryKey()
    {
        return ['tariff_id'];
    }

    /** {@inheritdoc} */
    public function init()
    {
        if (Yii::$app->user->isGuest) {
            $this->seller = Yii::$app->params['seller'];
        } else {
            $this->client = Yii::$app->user->identity->username;
        }

        $this->synchronize();
    }

    public function getValue()
    {
        return $this->hasMany(Value::class, ['tariff_id' => 'currency'])->indexBy('currency');
    }

    /**
     * @param string $currency
     *
     * @return Value
     * @throws InvalidParamException when the $currency is not calculated
     */
    public function forCurrency($currency)
    {
        if (!isset($this->value[$currency])) {
            throw new InvalidParamException("Calculation for currency \"$currency\" does not exist");
        }

        return $this->value[$currency];
    }

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['tariff_id', 'object', 'seller', 'client', 'type', 'currency', 'item'], 'safe'],
            [['amount'], 'number'],
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
