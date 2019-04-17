<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
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
        $data = Yii::$app->cache->getOrSet([__METHOD__, 'serversGetLocations'], function () {
            return \hipanel\modules\server\models\Server::Perform('getLocations',[
                Tariff::TYPE_XEN => ['type' => Tariff::TYPE_XEN],
                Tariff::TYPE_OPENVZ => ['type' => Tariff::TYPE_OPENVZ],
            ], ['batch' => true]);
        }, 3600);

        return $data[$this->resource->tariff->type];
    }
}
