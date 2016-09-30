<?php

namespace hipanel\modules\finance\cart;

class Calculation extends \hipanel\modules\finance\models\Calculation
{
    /**
     * @var AbstractCartPosition
     */
    public $position;

    public function synchronize()
    {
        $this->calculation_id = $this->position->getId();
        $this->amount = $this->position->getQuantity();
    }
}
