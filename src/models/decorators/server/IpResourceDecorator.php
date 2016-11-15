<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class IpResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel:server:order', 'Dedicated IP');
    }

    public function getOverusePrice()
    {
        return 4; // TODO: move to config
    }

    public function displayUnit()
    {
        return Yii::t('hipanel', 'IP');
    }

    public function displayValue()
    {
        return $this->resource->quantity;
    }


}
