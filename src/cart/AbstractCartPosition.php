<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use hipanel\modules\finance\models\CalculableModelInterface;
use hipanel\modules\finance\models\Calculation;
use hiqdev\hiart\ActiveRecord;
use hiqdev\yii2\cart\CartPositionInterface;
use hiqdev\yii2\cart\CartPositionTrait;
use Serializable;
use Yii;
use yii\base\InvalidConfigException;

/**
 * AbstractCartPosition represents position (item) in cart.
 * Holds:
 * - calculation object
 * - purchase object
 * - price for single item
 * - value for selected quantity
 *
 * @property Calculation $actionCalcModel
 */
abstract class AbstractCartPosition extends ActiveRecord implements CartPositionInterface, CalculableModelInterface, Serializable
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
     * {@inheritdoc}
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
        $this->_price = (float) $price;
    }

    /**
     * {@inheritdoc}
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
     * Example:.
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
     * @throws \yii\base\InvalidConfigException
     * @return Calculation
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
     * @throws \yii\base\InvalidConfigException
     * @return AbstractPurchase
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
     * Returns the value of the domain.
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
        $this->_value = (float) $value;
    }

    public function serialize()
    {
        return serialize($this->serializationMap());
    }

    /**
     * Method stores map of attributes that must be serialized.
     *
     * @return array key is the attribute name where the value should be extracted
     * In case when the value shoule be assigned in a special way, use [[unserializationMap]]
     * to map key to a closure, that will set the value.
     *
     * @see unserializationMap
     */
    protected function serializationMap()
    {
        return [
            'attributes' => $this->getAttributes(),
            '_quantity' => $this->_quantity,
            '_price' => $this->_price,
            '_value' => $this->_value,
            '_id' => $this->_id,
        ];
    }

    /**
     * @return array
     * Key is the attribute name, value - the closure to unserialize the value
     */
    protected function unserializationMap()
    {
        return [
            'attributes' => function ($value) {
                $this->setAttributes($value, false);
            },
        ];
    }

    public function unserialize($serialized)
    {
        $map = $this->unserializationMap();
        $array = unserialize($serialized);
        foreach ($array as $key => $value) {
            if (!isset($map[$key])) {
                $this->{$key} = $value;
                continue;
            }

            if ($map[$key] instanceof \Closure) {
                $map[$key]($value);
            }
        }
    }
}
