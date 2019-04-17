<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic\bill;

use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\server\models\Consumption;

interface QuantityFormatterFactoryInterface
{
    /**
     * @param object $model
     * @return QuantityFormatterInterface|null
     */
    public function create($model): ?QuantityFormatterInterface;

    /**
     * @param Bill|BillForm $bill
     * @return QuantityFormatterInterface|null
     */
    public function forBill($bill): ?QuantityFormatterInterface;

    /**
     * @param Charge $charge
     * @return QuantityFormatterInterface|null
     */
    public function forCharge(Charge $charge): ?QuantityFormatterInterface;

    /**
     * @param Consumption $consumption
     * @return QuantityFormatterInterface|null
     */
    public function forConsumption(Consumption $consumption): ?QuantityFormatterInterface;
}
