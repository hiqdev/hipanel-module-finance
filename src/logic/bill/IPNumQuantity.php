<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class IPNumQuantity extends AbstractBillQuantity
{
    public function getText()
    {
        return Yii::t('hipanel:finance', '{quantity} IP', ['quantity' => $this->getValue()]);
    }
}

