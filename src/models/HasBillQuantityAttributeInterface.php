<?php declare(strict_types=1);

namespace hipanel\modules\finance\models;

/**
 * @property string|null $quantity
 * @property int|string|float|null $bill_quantity
 */
interface HasBillQuantityAttributeInterface
{
    public function getQuantity();

    public function getBillQuantity();
}
