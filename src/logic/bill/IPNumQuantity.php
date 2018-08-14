<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

/**
 * Class IPNumQuantity
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class IPNumQuantity extends DefaultQuantityFormatter
{
    public function format(): string
    {
        return Yii::t('hipanel:finance', '{quantity} IP', ['quantity' => $this->getValue()]);
    }
}

