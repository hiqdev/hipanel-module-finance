<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

trait QuantityTrait
{
    public function getQuantity()
    {
        if (!$this->isNewRecord && isset($this->type)) {
            $factory = Yii::$container->get(BillQuantityFactoryInterface::class);
            $billQty = $factory->create($this);

            if ($billQty) {
                return $billQty->getClientValue();
            }
        }
    }
}