<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

declare(strict_types=1);

namespace hipanel\modules\finance\models\decorators\target;

use Yii;

class IpPublicResourceDecorator extends IpRegularResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel.finance.resource', 'Public IP');
    }
}
