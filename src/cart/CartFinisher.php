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
     * Runs the purchase.
     * Purchases the positions in the [[cart]].
     * @return array
     *  - 0 AbstractCartPosition[]: successfully purchased positions
     *  - 1 ErrorPurchaseException[]: errors in positions
     */
    public function run()
    {
        $success = [];
        $error = [];

        if (!$this->cart->isEmpty) {
            foreach ($this->cart->positions as $position) {
                $purchase = $position->getPurchaseModel();
                try {
                    $purchase->execute();

                    $success[] = clone $position;
                    $this->cart->remove($position);
                } catch (ErrorResponseException $e) {
                    $error[] = new ErrorPurchaseException($e->getMessage(), $position, $e);
                } catch (HiArtException $e) {
                    $error[] = new ErrorPurchaseException($e->getMessage(), $position, $e);
                }
            }
        }

        return [$success, $error];
    }
}
