<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class SupportResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', '24/7 support');
    }

    public function displayValue()
    {
        $quantity = $this->resource->quantity;

        if ($quantity === 1) {
            return Yii::t('hipanel/server/order', 'Bronze');
        } elseif ($quantity === 1.5) {
            return Yii::t('hipanel/server/order', 'Silver');
        } elseif ($quantity === 2) {
            return Yii::t('hipanel/server/order', 'Gold');
        } elseif ($quantity === 3) {
            return Yii::t('hipanel/server/order', 'Platinum');
        }

        return Yii::t('hipanel/server/order', '{n, plural, one{# hour} other{# hours}}', ['n' => $quantity]);
    }
}
