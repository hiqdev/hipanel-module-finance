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
 * Trait PurchaseResultTrait.
 */
trait PurchaseResultTrait
{
    protected $success = [];

    protected $error = [];

    protected $pending = [];

    protected function resetPurchaseResults()
    {
        $this->success = [];
        $this->error = [];
        $this->pending = [];
    }

    public function getErrorPurchaseExceptions()
    {
        return $this->error;
    }

    public function getSuccessPurchases()
    {
        return $this->success;
    }

    public function getPendingPurchaseExceptions()
    {
        return $this->pending;
    }
}
