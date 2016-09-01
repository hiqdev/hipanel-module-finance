<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class HddResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'SSD');
    }

    public function displayValue()
    {
        $part = $this->resource->part;
        preg_match('/((\d{1,5}) GB)$/i', $part->partno, $matches);
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => (int) $matches[2]]); // Gb
    }

    public function getOverusePrice()
    {
        return 0.2; // TODO: move to config
    }

    public function displayUnit()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => 1]);
    }
}
