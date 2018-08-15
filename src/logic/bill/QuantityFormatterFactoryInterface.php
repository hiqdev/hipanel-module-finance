<?php

namespace hipanel\modules\finance\logic\bill;

use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;

interface QuantityFormatterFactoryInterface
{
    /**
     * @param Bill $model
     * @return QuantityFormatterInterface|null
     */
    public function create(Bill $model): ?QuantityFormatterInterface;

    /**
     * @param Bill $bill
     * @return QuantityFormatterInterface|null
     */
    public function forBill(Bill $bill): ?QuantityFormatterInterface;

    /**
     * @param Charge $charge
     * @return QuantityFormatterInterface|null
     */
    public function forCharge(Charge $charge): ?QuantityFormatterInterface;
}
