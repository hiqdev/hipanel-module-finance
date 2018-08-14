<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

trait QuantityTrait
{
    public function getQuantity()
    {
        if (!$this->isNewRecord && isset($this->type)) {
            /** @var QuantityFormatterFactoryInterface $factory */
            $factory = Yii::$container->get(QuantityFormatterFactoryInterface::class);
            $billQty = $factory->create($this);

            if ($billQty !== null) {
                return $billQty->getClientValue();
            }
        }
    }
}
