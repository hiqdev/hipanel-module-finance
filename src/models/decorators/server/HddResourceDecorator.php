<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class HddResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/server/order', 'SSD');
    }

    public function displayPrepaidAmount()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => $this->getPrepaidQuantity()]); // Gb
    }

    public function getOverusePrice()
    {
        return 0.2; // TODO: move to config
    }

    public function getPrepaidQuantity()
    {
        $part = $this->resource->part;
        preg_match('/((\d{1,5}) GB)$/i', $part->partno, $matches);
        return (int) $matches[2];
    }

    public function displayUnit()
    {
        return Yii::t('hipanel', 'GB');
    }
}
