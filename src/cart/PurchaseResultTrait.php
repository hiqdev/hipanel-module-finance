<?php

namespace hipanel\modules\finance\cart;

/**
 * Trait PurchaseResultTrait
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
