<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class PanelResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel:finance:tariff', 'Control panel');
    }

    public function displayValue()
    {
        return null;
    }

    public function displayUnit()
    {
        return null;
    }

    public function displayPrepaidAmount()
    {
        $result = Yii::t('hipanel:finance:tariff', 'No panel / {hipanelLink}', ['hipanelLink' => 'HiPanel']); // todo: add faq link
        if ($this->resource->tariff->getResourceByType('isp5')->quantity > 0) {
            $result .= ' / ' . Yii::t('hipanel:finance:tariff', 'ISP manager');
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
