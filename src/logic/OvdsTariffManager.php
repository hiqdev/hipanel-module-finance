<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\OvdsTariffForm;

class OvdsTariffManager extends VdsTariffManager
{
    public $type = 'ovds';

    protected function getFormOptions()
    {
        return array_merge([
            'class' => OvdsTariffForm::class
        ], parent::getFormOptions());
    }
}
