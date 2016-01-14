<?php

namespace hipanel\modules\finance\cart;

use hipanel\modules\finance\models\Calculation;
use hiqdev\hiart\ErrorResponseException;
use hiqdev\hiart\HiArtException;
use hiqdev\yii2\cart\ShoppingCart;
use Yii;
use yii\base\Object;
use yii\web\UnprocessableEntityHttpException;
use yz\shoppingcart\CartActionEvent;

class CartFinisher extends Object
{
    /**
     * @var ShoppingCart
     */
    public $cart;

    /**
     * Runs the purchase.
     * Normally, the method should call [[finish]]
     * @void
     */
    public function run()
    {
        $this->finish();
    }

    /**
     * Finishes the positions in the [[cart]]
     *
     * @throws UnprocessableEntityHttpException
     */
    protected function finish()
    {
        foreach ($this->cart->positions as $position) {
            $purchase = $position->getPurchaseModel();
            try {
                $result = $purchase->execute();
                if ($result === true) {
                    $this->cart->remove($position);
                }
            } catch (ErrorResponseException $e) {
                throw new ErrorPurchaseException($e->getMessage(), $this->position, $e);
            } catch (HiArtException $e) {
                throw new ErrorPurchaseException($e->getMessage(), $this->position, $e);
            }
        }
    }
}