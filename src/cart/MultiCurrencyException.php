<?php

namespace hipanel\modules\finance\cart;

use hiqdev\yii2\cart\ShoppingCart;
use Yii;

final class MultiCurrencyException extends NotPurchasableException
{
    /**
     * @var AbstractCartPosition
     */
    private $position;
    /**
     * @var ShoppingCart
     */
    private $shoppingCart;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $message, ShoppingCart $shoppingCart, AbstractCartPosition $position)
    {
        parent::__construct($message ?: 'Can not add this item to cart because of different currency');
        $this->position = $position;
        $this->shoppingCart = $shoppingCart;
    }

    public function resolve(): bool
    {
        Yii::error('Cart position "' . $this->position->getName() . '" was removed from the cart because multi-currency cart is not available for now', 'hipanel.cart');
        $this->shoppingCart->remove($this->position);

        return true;
    }
}
