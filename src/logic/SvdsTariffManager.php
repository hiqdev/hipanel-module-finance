<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\SvdsTariffForm;

class SvdsTariffManager extends VdsTariffManager
{
    /** @inheritdoc */
    public $type = 'svds';

    /**
     * @inheritdoc
     */
    protected function buildForm()
    {
        $this->form = new SvdsTariffForm([
            'scenario' => $this->scenario,
            'baseTariffs' => $this->baseTariffs,
            'tariff' => $this->tariff
        ]);
    }
}
