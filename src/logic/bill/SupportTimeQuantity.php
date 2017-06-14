<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class SupportTimeQuantity extends AbstractBillQuantity implements BillQuantityInterface
{
    public function getText()
    {
        $text = Yii::t('hipanel:finance', '{quantity, time, HH:mm} hour(s)',
            ['quantity' => ceil($this->model->quantity * 3600)]);

        return $text;
    }

    public function getValue()
    {
        return $this->model->quantity;
    }
}
