<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\cart\Calculation;

interface CalculatorInterface
{
    /**
     * Calculates price for the model with $id.
     *
     * @param $id
     *
     * @return Calculation
     */
    public function getCalculation($id);

    /**
     * Returns calculations for all passed models.
     *
     * @return Calculation[]
     */
    public function getCalculations();
}
