<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\SvdsTariffForm;
use hipanel\modules\finance\models\Tariff;

class SvdsTariffManager extends VdsTariffManager
{
    /** {@inheritdoc} */
    public $type = Tariff::TYPE_XEN;

    protected function getFormOptions()
    {
        return array_merge([
            'class' => SvdsTariffForm::class,
        ], parent::getFormOptions());
    }
}
