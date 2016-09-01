<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class IpNumResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'Dedicated IP');
    }

    public function getOverusePrice()
    {
        return 4; // TODO: move to config
    }

    public function displayUnit()
    {
        return Yii::t('yii', '{n} IP', ['n' => 1]);
    }

    public function displayValue()
    {
        return $this->resource->quantity;
    }


}
