<?php

namespace hipanel\modules\finance\models;

/**
 * Interface CalculableModelInterface must be implemented by models that support
 * price calculation
 */
interface CalculableModelInterface
{
    /**
     * @return \hipanel\modules\finance\models\Calculation
     */
    public function getCalculationModel();
}
