<?php

namespace hipanel\modules\finance\logic\bill;

use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\server\models\Consumption;
use hiqdev\php\units\Quantity;
use Yii;

/**
 * Class MonthlyQuantity
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RackUnitQuantity extends MonthlyQuantity
{
    /** @var Charge */
    protected $model;

    /**
     * @inheritdoc
     */
    public function format(): string
    {
        $text = Yii::t('hipanel:finance', '{units, plural, one{# unit} other{# units}} &times; {quantity, plural, one{# day} other{# days}}', [
            'units' => $this->model->quantity / $this->model->bill->quantity,
            'quantity' => round($this->model->bill->quantity * $this->getNumberOfDays())
        ]);

        return $text;
    }

    public function getValue(): string
    {
        return $this->getQuantity()->getQuantity();
    }

    public function getClientValue(): string
    {
        return $this->getQuantity()->getQuantity();
    }
}
