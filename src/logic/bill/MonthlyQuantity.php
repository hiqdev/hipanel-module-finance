<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class MonthlyQuantity extends AbstractBillQuantity
{
    /**
     * @return string
     */
    public function getText()
    {
        $days = ceil($this->model->quantity * $this->getNumberOfDays());
        $text = Yii::t('hipanel:finance', '{quantity, plural, one{# day} other{# days}}', ['quantity' => $days]);

        return $text;
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        return $this->model->quantity / $this->getNumberOfDays();
    }

    /**
     * @return false|string
     */
    protected function getNumberOfDays()
    {
        return date('t', strtotime($this->model->time));
    }
}
