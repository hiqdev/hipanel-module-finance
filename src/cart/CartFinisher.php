<?php

/*
 * Finance Plugin for HiPanel
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
     * @var AbstractPurchase[] array of successfull purchases
     */
    protected $_success = [];

    /**
     * @var ErrorPurchaseException[] array of failed purchases
     */
    protected $_error = [];

    /**
     * Getter for array of successfull purchases.
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
     * Runs the purchase.
     * Purchases the positions in the [[cart]].
     */
    public function run()
    {
        if (!$this->cart->isEmpty) {
            foreach ($this->cart->positions as $position) {
                $purchase = $position->getPurchaseModel();
                try {
                    $purchase->execute();
                    $this->_success[] = $purchase;
                    $this->cart->remove($position);
                } catch (ErrorResponseException $e) {
                    $this->_error[] = new ErrorPurchaseException($e->getMessage(), $position, $e);
                } catch (HiArtException $e) {
                    $this->_error[] = new ErrorPurchaseException($e->getMessage(), $position, $e);
                }
            }
        }
    }
}
