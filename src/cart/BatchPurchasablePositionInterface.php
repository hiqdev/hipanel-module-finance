<?php

namespace hipanel\modules\finance\cart;

/**
 * Interface BatchPurchasablePositionInterface marks position that is suitable for batch
 * purchase operation.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface BatchPurchasablePositionInterface
{
    /**
     * @return string class name for batch purchase processing
     */
    public function getBatchPurchaseStrategyClass();
}
