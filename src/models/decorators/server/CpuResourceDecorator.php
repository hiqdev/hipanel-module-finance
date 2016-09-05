<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class CpuResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'CPU');
    }

    public function displayPrepaidAmount()
    {
        return Yii::t('hipanel/server/order', '{0, plural, one{# core} other{# cores}}', $this->getPrepaidQuantity());
    }

    public function getPrepaidQuantity()
    {
        preg_match('/((\d+) cores?)$/i', $this->resource->part->partno, $matches);
        return $matches[2] === null ? 0 : $matches[2];
    }
}
