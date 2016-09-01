<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class Traffic95ResourceDecorator extends TrafficResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', '95 percentile traffic');
    }
}
