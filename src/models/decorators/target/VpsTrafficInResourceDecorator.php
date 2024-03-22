<?php
declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2022, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\target;

use hipanel\modules\finance\models\decorators\server\TrafficResourceDecorator;
use Yii;

class VpsTrafficInResourceDecorator extends TrafficResourceDecorator
{
    public function displayTitle(): string
    {
        return Yii::t('hipanel.finance.resource', 'VPS Traffic IN');
    }
}
