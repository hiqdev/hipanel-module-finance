<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use hiqdev\hiart\ErrorResponseException;
use hiqdev\hiart\HiArtException;
use hiqdev\yii2\cart\ShoppingCart;
use yii\base\Object;

class CartFinisher extends Object
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
        if (!$this->cart->isEmpty) {
            foreach ($this->cart->positions as $position) {
                $purchase = $position->getPurchaseModel();
                try {
                    if ($purchase->execute()) {
                        $this->_success[] = $position;
                        $this->cart->remove($position);
                    } else {
                        $this->_error[] = new ErrorPurchaseException(reset(reset($purchase->getErrors())), $purchase);
                    }
                } catch (PendingPurchaseException $e) {
                    $this->_pending[] = $e;
                    $this->cart->remove($position);
                } catch (ErrorResponseException $e) {
                    $this->_error[] = new ErrorPurchaseException($e->getMessage(), $purchase, $e);
                } catch (HiArtException $e) {
                    $this->_error[] = new ErrorPurchaseException($e->getMessage(), $purchase, $e);
                }
            }
        }
    }
}
