<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */


namespace hipanel\modules\finance\models\decorators\target;

use hipanel\modules\finance\models\decorators\server\AbstractServerResourceDecorator;
use Yii;

class VpsIpRegularResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel.finance.resource', 'Regular IP');
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
