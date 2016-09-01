<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class BackupResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'Backup usage');
    }

    public function displayValue()
    {
        return $this->resource->quantity;
    }
}
