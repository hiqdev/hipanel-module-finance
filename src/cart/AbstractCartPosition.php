<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use hipanel\modules\finance\models\AbstractPurchase;
use hipanel\modules\finance\models\Calculation;
use hiqdev\hiart\ActiveRecord;
use hiqdev\yii2\cart\CartPositionInterface;
use hiqdev\yii2\cart\CartPositionTrait;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class AbstractCartPosition
 * @property Calculation $actionCalcModel
 */
abstract class AbstractCartPosition extends ActiveRecord implements CartPositionInterface
{
    use CartPositionTrait;

    /**
     * @var string|array|Calculation
     *  - string: the action calculation model class name
     *  - array: array of options for [[Yii::createObject()]] call
     *  - object: the object that extends [[Calculation]] class and represents calculation of the specified object type
     */
    protected $_calculationModel;

    /**
     * @var string|array
     *  - string: the position purchase model class name
     *  - array: array of options for [[Yii::createObject()]] call
     */
    protected $_purchaseModel;

    /**
     * @var double the price of the 1 piece of the position
     */
    protected $_price;

    /**
     * @var double the full value of position
     */
    protected $_value;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->_purchaseModel === null) {
            throw new InvalidConfigException('Purchase model is not defined. The position can not be ordered');
        }
    }

    /**
     * @return double
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * Sets the [[price]].
     *
     * The $price will be casted to double
     *
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->_price = (double)$price;
    }

    /**
     * @inheritdoc
     */
    public function getCost($withDiscount = true)
    {
        if ($withDiscount) {
            return $this->getValue();
        } else {
            return $this->getQuantity() * $this->getPrice();
        }
    }

    /**
     * The method returns the `crc32b` hash of a distinct condition
     * Example:
     *
     * ```php
     *    return hash('crc32b', implode('_', ['domain', 'registration', $this->name]));
     * ```
     *
     * @return string
     */
    abstract public function getId();

    /**
     * @param array $options Options that override defaults on [[Yii::createObject()]]
     * @return Calculation
     * @throws \yii\base\InvalidConfigException
     */
    public function getCalculationModel($options = [])
    {
        if (!($this->_calculationModel instanceof Calculation)) {
            $config = ['position' => $this];

            if (is_string($this->_calculationModel)) {
                $config['class'] = $this->_calculationModel;
            }

            $this->_calculationModel = Yii::createObject(array_merge($config, $options));
        } else {
            $this->_calculationModel->synchronize();
        }

        return $this->_calculationModel;
    }

    /**
     * @param array $options Options that override defaults on [[Yii::createObject()]]
     * @return AbstractPurchase
     * @throws \yii\base\InvalidConfigException
     */
    public function getPurchaseModel($options = [])
    {
        $config = ['position' => $this];

        if (is_string($this->_purchaseModel)) {
            $config['class'] = $this->_purchaseModel;
        }

        return Yii::createObject(array_merge($config, $options));
    }

    /**
     * Returns the value of the domain
     * @return double
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets the [[value]].
     *
     * The $value will be casted to double
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->_value = (double)$value;
    }
}
