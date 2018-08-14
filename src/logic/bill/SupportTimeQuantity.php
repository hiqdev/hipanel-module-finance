<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class SupportTimeQuantity extends DefaultQuantityFormatter
{
    public function format(): string
    {
        return Yii::t('hipanel:finance', '{quantity, time, HH:mm} hour(s)', [
            'quantity' => ceil($this->getQuantity()->getQuantity() * 3600)
        ]);
    }
}
