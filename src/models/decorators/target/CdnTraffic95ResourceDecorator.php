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

use hipanel\modules\finance\models\decorators\server\TrafficResourceDecorator;
use Yii;

class CdnTraffic95ResourceDecorator extends TrafficResourceDecorator
{
    public function displayTitle()
    {
        return $this->displayTitleWithDirection(Yii::t('hipanel.finance.resource', 'CDN Traffic 95 percentile'));
    }
}
