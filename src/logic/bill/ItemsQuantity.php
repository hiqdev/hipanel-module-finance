<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class ItemsQuantity extends AbstractBillQuantity
{
    public function getText()
    {
        return Yii::t('hipanel:finance', '{quantity, plural, one{# item} other{# items}}', ['quantity' => $this->getValue()]);
    }
}
