<?php

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class PanelResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel/finance/tariff', 'Control panel');
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => $this->getPrepaidQuantity()]);
    }

    public function displayUnit()
    {
        return Yii::t('hipanel/finance/tariff', '{n} Gbit/s', ['n' => 1]);
    }

    public function displayPrepaidAmount()
    {
        $result = Yii::t('hipanel/finance/tariff', 'No panel / {hipanelLink}', ['hipanelLink' => 'HiPanel']); // todo: add faq link
        if ($this->resource->tariff->getResourceByType('isp5')->quantity > 0) {
            $result .= ' / ' . Yii::t('hipanel/finance/tariff', 'ISP manager');
        }

        return $result;
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
