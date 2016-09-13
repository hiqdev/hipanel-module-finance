<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\SvdsTariffForm;
use hipanel\modules\finance\models\Tariff;

class SvdsTariffManager extends VdsTariffManager
{
    /** @inheritdoc */
    public $type = Tariff::TYPE_XEN;

    protected function getFormOptions()
    {
        return array_merge([
            'class' => SvdsTariffForm::class
        ], parent::getFormOptions());
    }
}
