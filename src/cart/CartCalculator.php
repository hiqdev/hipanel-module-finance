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

use hipanel\modules\finance\logic\Calculator;
use hipanel\modules\finance\models\Calculation;
use hipanel\modules\finance\models\Value;
use hiqdev\yii2\cart\ShoppingCart;
use Yii;
use yz\shoppingcart\CartActionEvent;

/**
 * Class CartCalculator provides API to calculate [[cart]] positions value.
 *
 * Usage:
 *
 * ```php
 * $calculator = new CartCalculator([
 *     'cart' => $this->cart
 * ]);
 *
 * $calculator->run(); // will calculate prices for all cart positions and update them
 * ```
 *
 * Also can be bound to some cart event as handler:
 *
 * ```php
 * $cart->on(Cart::EVENT_UPDATE, [CartCalculator::class, 'handle']);
 * ```
 */
final class CartCalculator extends Calculator
{
    /**
     * @var array
     */
    private static $ignoreIds = [];

    /**
     * @var AbstractCartPosition[]
     */
    protected $models;

    /**
     * @var ShoppingCart
     */
    public $cart;

    /**
     * @var CartActionEvent
     */
    public $event;

    /**
     * Creates the instance of the object and runs the calculation.
     *
     * @param CartActionEvent $event The event
     * @void
     */
    public static function handle($event)
    {
        /** @var ShoppingCart $cart */
        $cart = $event->sender;

        $calculator = new static($cart);

        return $calculator->execute();
    }

    /**
     * @param ShoppingCart $cart
     */
    public function __construct(ShoppingCart $cart)
    {
        $this->cart = $cart;

        parent::__construct($this->cart->positions);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        parent::execute();

        $this->applyCalculations();

        return $this->calculations;
    }

    /**
     * Updates positions using the calculations provided with [[getCalculation]].
     */
    private function applyCalculations()
    {
        $currency = Yii::$app->params['currency'];

        foreach ($this->models as $position) {
            $id = $position->id;
            if (in_array($id, array_values(self::$ignoreIds))) {
                throw new ErrorMultiCurrencyException(Yii::t('cart', 'Sorry, but now it is impossible to add the position with different currencies to the cart. Pay the current order to add this item to the cart.'), $position->getPurchaseModel());
            }

            $calculation = $this->getCalculation($id);
            if (!$calculation instanceof Calculation) {
                Yii::error('Cart position "' . $position->getName() . '" was removed from the cart because of failed value calculation. Normally this should never happen.', 'hipanel.cart');
                $this->cart->removeById($position->id);
                break;
            }

            /** @var Value $value */
            $value = $calculation->forCurrency($currency);
            if (!$value instanceof Value) {
                Yii::error('Cart position "' . $position->getName() . '" was removed from the cart because calculation for currency "' . $value->currency . '" is not available', 'hipanel.cart');
                $this->cart->removeById($position->id);
                break;
            }
            if ($this->cart->getCurrency() && $value->currency !== $this->cart->getCurrency()) {
                self::$ignoreIds[] = $id;
                $this->cart->removeById($id);
                Yii::error('Cart position "' . $position->getName() . '" was removed from the cart because multi-currency cart is not available for now', 'hipanel.cart');
                break;
            }

            $position->setPrice($value->price);
            $position->setValue($value->value);
            $position->setCurrency($value->currency);
        }
    }
}
