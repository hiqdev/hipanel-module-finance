<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\SvdsTariffForm;

class SvdsTariffManager extends VdsTariffManager
{
    /** @inheritdoc */
    public $type = 'svds';

    protected function getFormOptions()
    {
        return array_merge([
            'class' => SvdsTariffForm::class
        ], parent::getFormOptions());
    }
}
