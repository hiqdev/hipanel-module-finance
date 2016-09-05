<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class BackupResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'Backup usage');
    }

    public function displayUnit()
    {
        return Yii::t('hipanel', 'GB');
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => $this->getPrepaidQuantity()]);
    }
}
