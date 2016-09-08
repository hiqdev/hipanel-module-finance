<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class SpeedResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/finance/tariff', 'Port speed');
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => $this->getPrepaidQuantity()]);
    }

    public function displayUnit()
    {
        return Yii::t('hipanel/finance/tariff', 'Gbit/s');
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
