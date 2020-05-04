<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use hipanel\modules\finance\models\Bill;
use hiqdev\yii2\cart\NotPurchasableException;
use hiqdev\yii2\cart\ShoppingCart;
use Yii;
use yii\base\BaseObject;
use yii\web\User;

class CartFinisher extends BaseObject
{
    /**
     * @var ShoppingCart
     */
    public $cart;

    /**
     * @var string|null
     */
    public $exchangeFromCurrency;

    /**
     * @var User
     */
    public $user;

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
        $this->exchangeMoney();

        foreach ($this->purchasers as $purchaser) {
            $purchaser->run();

            $this->cart->accumulateEvents(function () use ($purchaser) {
                $this->_success = array_merge($this->_success, $purchaser->getSuccessPurchases());
                foreach ($purchaser->getSuccessPurchases() as $purchase) {
                    $this->cart->remove($purchase->position);
                }
                $this->_pending = array_merge($this->_pending, $purchaser->getPendingPurchaseExceptions());
                foreach ($purchaser->getPendingPurchaseExceptions() as $exception) {
                    $this->cart->remove($exception->position);
                }
            });
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
        } catch (NotPurchasableException $e) {
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
            $this->purchasers[$positionClass] = new $purchaserClass($this->cart, $this->user);
        }

        return $this->purchasers[$positionClass];
    }

    private function exchangeMoney(): void
    {
        if ($this->exchangeFromCurrency === null) {
            return;
        }

        Bill::perform('create-exchange', [
            'from' => $this->exchangeFromCurrency,
            'to' => $this->cart->getCurrency(),
            'buySum' => $this->cart->getTotal(),
        ]);
    }
}
