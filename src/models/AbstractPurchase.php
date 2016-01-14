<?php

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\cart\AbstractCartPosition;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class Purchase
 * @package hipanel\modules\finance\models
 */
abstract class AbstractPurchase extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /**
     * @var AbstractCartPosition
     */
    protected $position;

    /** @inheritdoc */
    public static function index()
    {
        throw new InvalidConfigException('Method "index" must be declared');
    }

    /** @inheritdoc */
    public static function type()
    {
        throw new InvalidConfigException('Method "index" must be declared');

    }

    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['cart_position_id'];
    }

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        $this->synchronize();
    }

    /**
     * Synchronises the model to represent actual state of [[position]]
     * The method must update values, that affects the calculation and
     * can be changed in cart without position re-adding.
     * For example: quantity
     */
    public function synchronize()
    {
        $this->cart_position_id = $this->position->getId();
        $this->amount = $this->position->getQuantity();
    }

    /**
     * Executes the purchase.
     * Calls proper API commands to purchase the product.
     * @return boolean whether the item was purchased
     */
    abstract public function execute();

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['cart_position_id', 'object', 'client', 'type', 'currency', 'item'], 'safe'],
            [['amount'], 'number'],
        ];
    }
}