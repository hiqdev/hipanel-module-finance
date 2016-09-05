<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class Isp5ResourceDecorator extends IspResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'ISP Manager 5');
    }

    public function displayPrepaidAmount()
    {
        return $this->getPrepaidQuantity() > 0 ? Yii::t('hipanel', 'Enabled') : Yii::t('hipanel', 'Disabled');
    }
}
