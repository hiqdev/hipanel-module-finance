<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

/**
 * Interface CalculableModelInterface must be implemented by models that support
 * price calculation.
 */
interface CalculableModelInterface
{
    /**
     * @return \hipanel\modules\finance\models\Calculation
     */
    public function getCalculationModel();
}
