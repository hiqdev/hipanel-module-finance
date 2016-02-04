<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\cart\AbstractCartPosition;
use hipanel\modules\finance\cart\ErrorPurchaseException;
use yii\base\InvalidConfigException;

/**
 * Class Purchase.
 */
abstract class AbstractPurchase extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /**
     * @var AbstractCartPosition
     */
    public $position;

    /** {@inheritdoc} */
    public static function index()
    {
        throw new InvalidConfigException('Method "index" must be declared');
    }

    /** {@inheritdoc} */
    public static function type()
    {
        throw new InvalidConfigException('Method "index" must be declared');
    }

    /** {@inheritdoc} */
    public static function primaryKey()
    {
        return ['cart_position_id'];
    }

    /** {@inheritdoc} */
    public function init()
    {
        parent::init();

        $this->cart_position_id = $this->position->getId();
        $this->amount = $this->position->getQuantity();
    }

    /**
     * Executes the purchase.
     * Calls proper API commands to purchase the product.
     * @throws ErrorPurchaseException in case of failed purchase
     * @return true if the item was purchased successfully
     */
    abstract public function execute();

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['cart_position_id', 'object', 'client', 'type', 'currency', 'item'], 'safe'],
            [['amount'], 'number'],
        ];
    }
}
