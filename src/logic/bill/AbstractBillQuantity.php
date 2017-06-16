<?php

namespace hipanel\modules\finance\logic\bill;

use hipanel\base\Model;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;

abstract class AbstractBillQuantity implements BillQuantityInterface
{
    /**
     * @var Bill|Charge
     */
    public $model;

    /**
     * MonthlyQuantity constructor.
     * @param $model Model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getText()
    {
        return null;
    }

    public function getValue()
    {
        return $this->model->quantity;
    }

    public function getClientValue()
    {
        return $this->getValue();
    }
}