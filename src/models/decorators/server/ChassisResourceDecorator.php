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

class ChassisResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel:server:order', 'Chassis');
    }

    public function displayPrepaidAmount()
    {
        return $this->resource->part->partno;
    }
}
