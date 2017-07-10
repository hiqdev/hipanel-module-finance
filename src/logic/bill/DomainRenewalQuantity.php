<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class DomainRenewalQuantity extends AbstractBillQuantity
{
    /**
     * @inheritdoc
     */
    public function getText()
    {
        $text = Yii::t('hipanel:finance', '{quantity, plural, one{# year} other{# years}}', ['quantity' => $this->getClientValue()]);

        return $text;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->model->quantity;
    }

    /**
     * @inheritdoc
     */
    public function getClientValue()
    {
        return $this->model->quantity;
    }
}
