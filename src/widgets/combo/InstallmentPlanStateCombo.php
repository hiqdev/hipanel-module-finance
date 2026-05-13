<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

declare(strict_types=1);

namespace hipanel\modules\finance\widgets\combo;

use hipanel\widgets\RefCombo;

class InstallmentPlanStateCombo extends RefCombo
{
    public $gtype = 'state,installment_plan';
}
