<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class SpeedResourceDecorator extends AbstractServerResourceDecorator
{
    public function __construct()
    {
        return;
    }

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
        return Yii::t('hipanel/server/order', '{n} Gbit/s', ['n' => 1]);
    }

    public function getPrepaidQuantity()
    {
        return 1;
    }

    public function getOverusePrice()
    {
        return null;
    }

    public function displayOverusePrice()
    {
        return null;
    }
}
