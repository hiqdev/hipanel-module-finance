<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class CpuResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'CPU');
    }

    public function displayValue()
    {
        preg_match('/((\d+) cores?)$/i', $this->resource->part->partno, $matches);
        $matches[2] = $matches[2] === null ? 0 : $matches[2];
        return Yii::t('hipanel/server/order', '{0, plural, one{# core} other{# cores}}', $matches[2]);
    }
}
