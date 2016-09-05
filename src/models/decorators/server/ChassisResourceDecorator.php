<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class ChassisResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'Chassis');
    }

    public function displayPrepaidAmount()
    {
        return $this->resource->part->partno;
    }
}
