<?php

namespace hipanel\modules\finance\models\decorators\server;

use hipanel\inputs\BooleanInput;
use Yii;

class IspResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'ISP Manager');
    }

    public function displayPrepaidAmount()
    {
        return $this->getPrepaidQuantity() > 0 ? $this->amountOptions()[1] : $this->amountOptions()[0];
    }

    public function prepaidAmountType()
    {
        return new BooleanInput($this->amountOptions());
    }

    private function amountOptions()
    {
        return [0 => Yii::t('hipanel', 'Disabled'), 1 => Yii::t('hipanel', 'Enabled')];
    }
}
