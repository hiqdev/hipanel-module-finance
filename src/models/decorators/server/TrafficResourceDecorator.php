<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class TrafficResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'Traffic');
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => $this->resource->quantity]);
    }

    public function displayUnit()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => 1]);
    }
}
