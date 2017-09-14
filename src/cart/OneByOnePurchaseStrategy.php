<?php

namespace hipanel\modules\finance\cart;

use hiqdev\hiart\ResponseErrorException;
use hiqdev\yii2\cart\ShoppingCart;

/**
 * Class OneByOnePurchaseStrategy is used to purchase positions one-by-one.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class OneByOnePurchaseStrategy implements PurchaseStrategyInterface
{
    use PurchaseResultTrait;

    /**
     * @var ShoppingCart
     */
    protected $cart;

    /**
     * @var AbstractCartPosition[]
     */
    protected $positions = [];

    /**
     * OneByOnePurchaseStrategy constructor.
     *
     * @param ShoppingCart $cart
     */
    public function __construct(ShoppingCart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function addPosition(AbstractCartPosition $position)
    {
        $this->positions[$position->getId()] = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->resetPurchaseResults();

        foreach ($this->positions as $position) {
            $this->purchase($position);
        }
    }

    protected function purchase(AbstractCartPosition $position)
    {
        $purchase = $position->getPurchaseModel();

        try {
            if ($purchase->execute()) {
                $this->success[] = $purchase;
                return;
            }

            $this->error[] = new ErrorPurchaseException(reset(reset($purchase->getErrors())), $purchase);
        } catch (PendingPurchaseException $e) {
            $this->pending[] = $e;
        } catch (ResponseErrorException $e) {
            $this->error[] = new ErrorPurchaseException($e->getMessage(), $purchase, $e);
        } catch (\hiqdev\hiart\Exception $e) {
            $this->error[] = new ErrorPurchaseException($e->getMessage(), $purchase, $e);
        }
    }
}
