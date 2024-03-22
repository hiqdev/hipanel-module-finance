<?php

namespace hipanel\modules\finance\models\decorators\client;

use Yii;

class ReferralResourceDecorator extends AbstractClientResourceDecorator
{
    public function displayTitle(): string
    {
        return Yii::t('hipanel:client', 'Referral');
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted}', ['nFormatted' => $this->getPrepaidQuantity()]);
    }

    public function displayAmountWithUnit(): string
    {
        return Yii::$app->formatter->asCurrency($this->getPrepaidQuantity(), $this->displayUnit());
    }
}
