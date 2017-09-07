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

use hiqdev\hiart\ResponseErrorException;
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

        foreach ($this->cart->positions as $position) {
            $purchase = $position->getPurchaseModel();

            try {
                if ($purchase->execute()) {
                    $this->_success[] = $purchase;
                    $this->cart->remove($position);
                } else {
                    $this->_error[] = new ErrorPurchaseException(reset(reset($purchase->getErrors())), $purchase);
                }
            } catch (PendingPurchaseException $e) {
                $this->_pending[] = $e;
                $this->cart->remove($position);
            } catch (ResponseErrorException $e) {
                $this->_error[] = new ErrorPurchaseException($e->getMessage(), $purchase, $e);
            } catch (\hiqdev\hiart\Exception $e) {
                $this->_error[] = new ErrorPurchaseException($e->getMessage(), $purchase, $e);
            }
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
}
