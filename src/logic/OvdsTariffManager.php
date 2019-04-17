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

use hipanel\modules\finance\forms\OvdsTariffForm;
use hipanel\modules\finance\models\Tariff;

class OvdsTariffManager extends VdsTariffManager
{
    public $type = Tariff::TYPE_OPENVZ;

    protected function getFormOptions()
    {
        return array_merge([
            'class' => OvdsTariffForm::class,
        ], parent::getFormOptions());
    }
}
