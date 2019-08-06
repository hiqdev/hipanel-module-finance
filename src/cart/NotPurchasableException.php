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

use hiqdev\yii2\cart\ShoppingCart;
use yii\base\Exception;

/**
 * Interface PositionPurchasabilityValidatorInterface is thrown when cart
 * invariant validation restricts further purchase.
 */
abstract class NotPurchasableException extends Exception
{
    /** @var AbstractCartPosition|null */
    protected $position;

    /**
     * @var ShoppingCart
     */
    protected $cart;

    public static function forPosition(AbstractCartPosition $position, ShoppingCart $cart, string $message = '')
    {
        $exception = new static($message);

        $exception->position = $position;
        $exception->cart = $cart;

        return $exception;
    }

    public static function forCart(ShoppingCart $cart, string $message = '')
    {
        $exception = new static($message);

        $exception->cart = $cart;

        return $exception;
    }

    public function getName()
    {
        return 'Position is not purchasable';
    }

    /**
     * Method SHOULD BE called when exception is caught.
     * Child classes may override this method and add problem auto-resolving.
     *
     * @return bool whether exception was automatically resolved
     */
    public function resolve(): bool
    {
        return false;
    }
}
