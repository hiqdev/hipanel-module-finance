<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

class Calculation extends \hipanel\modules\finance\models\Calculation
{
    /**
     * @var AbstractCartPosition
     */
    public $position;

    public function synchronize()
    {
        if (isset($this->position)) {
            $this->calculation_id = $this->position->getId();
            $this->amount = $this->position->getQuantity();
        }
    }
}
