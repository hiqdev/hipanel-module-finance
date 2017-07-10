<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class MonthlyQuantity extends AbstractBillQuantity
{
    /**
     * @inheritdoc
     */
    public function getText()
    {
        $text = Yii::t('hipanel:finance', '{quantity, plural, one{# day} other{# days}}', ['quantity' => $this->getClientValue()]);

        return $text;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->model->quantity / $this->getNumberOfDays();
    }

    /**
     * @inheritdoc
     */
    public function getClientValue()
    {
        return round($this->model->quantity * $this->getNumberOfDays());
    }

    /**
     * @return false|string
     */
    protected function getNumberOfDays()
    {
        return date('t', strtotime($this->model->time));
    }
}
