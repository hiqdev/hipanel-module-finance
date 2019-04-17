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

/**
 * Interface PurchaseStrategyInterface declares API for purchase strategy.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface PurchaseStrategyInterface
{
    /**
     * Adds $position for further processing.
     *
     * @param AbstractCartPosition $position
     */
    public function addPosition(AbstractCartPosition $position);

    /**
     * Executes purchase of positions added using [[addPosition]] method.
     * After the purchase execution, the implementation must mark each position as
     * success, error or pending so methods [[getSuccessPurchases()]], [[getErrorPurchaseExceptions]],
     * [[getPendingPurchaseExceptions]] will return execution result.
     * @void
     */
    public function run();

    /**
     * Returns array of success purchases.
     * @return AbstractPurchase[]
     */
    public function getSuccessPurchases();

    /**
     * Returns array of error purchases.
     * @return ErrorPurchaseException[]
     */
    public function getErrorPurchaseExceptions();

    /**
     * Returns array of pending purchases.
     * @return PendingPurchaseException[]
     */
    public function getPendingPurchaseExceptions();
}
