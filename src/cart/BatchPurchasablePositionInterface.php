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
