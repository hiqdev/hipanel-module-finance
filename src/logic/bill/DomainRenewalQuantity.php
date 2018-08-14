<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class DomainRenewalQuantity extends DefaultQuantityFormatter
{
    /**
     * @inheritdoc
     */
    public function format(): string
    {
        return Yii::t('hipanel:finance', '{quantity, plural, one{# year} other{# years}}', [
            'quantity' => $this->getClientValue()
        ]);
    }
}
