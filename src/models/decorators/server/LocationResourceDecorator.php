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

use hipanel\inputs\OptionsInput;
use hipanel\modules\finance\models\Tariff;
use Yii;

class LocationResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel:finance:tariff', 'Location');
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
        return null;
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

    public function prepaidAmountType()
    {
        return new OptionsInput($this->amountOptions());
    }

    public function displayShortenLocations()
    {
        $data = $this->amountOptions();
        $result = [];
        foreach ($data as $item) {
            $result[] = substr($item, 0, strpos($item, ','));
        }
        $result = array_unique($result, SORT_STRING);

        return rtrim(implode(', ', $result), ', ');
    }

    private function amountOptions()
    {
        $data = [
            Tariff::TYPE_XEN => [
                1 => Yii::t('hipanel:finance:tariff', 'Netherlands, Amsterdam'),
                2 => Yii::t('hipanel:finance:tariff', 'USA, Ashburn'),
            ],
            Tariff::TYPE_OPENVZ => [
                2 => Yii::t('hipanel:finance:tariff', 'USA, Ashburn'),
                // Disabled on RED demand
                // 3 => Yii::t('hipanel:finance:tariff', 'Netherlands, Amsterdam')
            ],
        ];

        return $data[$this->resource->tariff->type];
    }
}
