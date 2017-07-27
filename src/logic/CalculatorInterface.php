<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\cart\Calculation;

interface CalculatorInterface
{
    /**
     * Calculates price for the model with $id
     *
     * @param $id
     *
     * @return Calculation
     */
    public function getCalculation($id);

    /**
     * Returns calculations for all passed models
     *
     * @return Calculation[]
     */
    public function getCalculations();
}
