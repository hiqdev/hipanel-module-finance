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

use hipanel\modules\finance\models\decorators\server\ServerResourceDecoratorFactory;

class TargetResourceDecoratorFactory extends ServerResourceDecoratorFactory
{
    protected static function typeMap(): array
    {
        return array_merge(parent::typeMap(), [
            'cdn_traf' => CdnTrafficResourceDecorator::class,
            'cdn_traf_max' => CdnTrafficResourceDecorator::class,
        ]);
    }
}
