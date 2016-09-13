<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\OvdsTariffForm;
use hipanel\modules\finance\models\Tariff;

class OvdsTariffManager extends VdsTariffManager
{
    public $type = Tariff::TYPE_OPENVZ;

    protected function getFormOptions()
    {
        return array_merge([
            'class' => OvdsTariffForm::class
        ], parent::getFormOptions());
    }
}
