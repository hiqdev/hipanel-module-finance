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

use hiqdev\yii2\cart\ShoppingCart;
use Yii;
use yii\base\BaseObject;

class CartFinisher extends BaseObject
{
    /**
     * @var ShoppingCart
     */
    public $cart;

    /**
     * @var PurchaseStrategyInterface[]
     */
    protected $purchasers = [];

    /**
     * @var AbstractPurchase[] array of successful purchases
     */
    protected $_success = [];

    /**
     * @var ErrorPurchaseException[] array of failed purchases
     */
    protected $_error = [];

    /**
     * @var PendingPurchaseException[] array of purchases that are pending
     */
    protected $_pending = [];

    /**
     * Getter for array of successful purchases.
     * @return AbstractPurchase[]
     */
    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * Getter for array of failed purchases.
     * @return ErrorPurchaseException[]
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Getter for array of failed purchases.
     * @return PendingPurchaseException[]
     */
    public function getPending()
    {
        return $this->_pending;
    }

    /**
     * Runs the purchase.
     * Purchases the positions in the [[cart]].
     */
    public function run()
    {
        if ($this->cart->isEmpty) {
            return;
        }

        $this->ensureCanBeFinished();
        $this->createPurchasers();

        foreach ($this->purchasers as $purchaser) {
            $purchaser->run();

            $this->_success = array_merge($this->_success, $purchaser->getSuccessPurchases());
            foreach ($purchaser->getSuccessPurchases() as $purchase) {
                $this->cart->remove($purchase->position);
            }
            $this->_pending = array_merge($this->_pending, $purchaser->getPendingPurchaseExceptions());
            foreach ($purchaser->getPendingPurchaseExceptions() as $exception) {
                $this->cart->remove($exception->position);
            }
            $this->_error = array_merge($this->_error, $purchaser->getErrorPurchaseExceptions());
        }
    }

    protected function ensureCanBeFinished()
    {
        /** @var PositionPurchasabilityValidatorInterface[] $validators */
        $validators = [];

        foreach ($this->cart->positions as $position) {
            $purchase = $position->getPurchaseModel();

            foreach ($purchase->getPurchasabilityRules() as $validator) {
                if (!isset($validators[$validator])) {
                    $validators[$validator] = Yii::createObject($validator);
                }
            }
        }

        try {
            foreach ($validators as $validator) {
                $validator->validate($this->cart->positions);
            }
        } catch (NotPurchasablePositionException $e) {
            $e->resolve();
        }
    }

    protected function createPurchasers()
    {
        foreach ($this->cart->positions as $position) {
            if ($position instanceof BatchPurchasablePositionInterface) {
                $purchaser = $this->getPurchaser(get_class($position), $position->getBatchPurchaseStrategyClass());
            } else {
                $purchaser = $this->getPurchaser(get_class($position), OneByOnePurchaseStrategy::class);
            }

            $purchaser->addPosition($position);
        }
    }

    /**
     * @param string $positionClass
     * @param string $purchaserClass
     * @return PurchaseStrategyInterface
     */
    protected function getPurchaser($positionClass, $purchaserClass)
    {
        if (!isset($this->purchasers[$positionClass])) {
            $this->purchasers[$positionClass] = new $purchaserClass($this->cart);
        }

        return $this->purchasers[$positionClass];
    }
}
