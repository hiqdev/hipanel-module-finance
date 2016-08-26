<?php

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\models\DomainResource;
use hipanel\modules\finance\models\DomainService;
use hipanel\modules\finance\models\Tariff;
use yii\web\UnprocessableEntityHttpException;

class VdsTariffForm extends AbstractTariffForm
{
    public function load($data)
    {
        $this->setAttributes($data[$this->formName()]);
        $this->setResources($data[(new DomainResource())->formName()]);

        return true;
    }
}
