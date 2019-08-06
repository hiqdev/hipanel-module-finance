<?php

namespace hipanel\modules\finance\cart;

use hiqdev\yii2\cart\NotPurchasableException;
use Yii;

final class MultiCurrencyException extends NotPurchasableException
{
    public function resolve(): bool
    {
        Yii::error('Cart position "' . $this->position->getName() . '" was removed from the cart because multi-currency cart is not available for now', 'hipanel.cart');
        $this->cart->remove($this->position);

        return true;
    }
}
