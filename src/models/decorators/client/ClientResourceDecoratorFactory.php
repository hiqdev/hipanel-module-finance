<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\client;

use hipanel\modules\finance\models\decorators\ResourceDecoratorFactory;

class ClientResourceDecoratorFactory extends ResourceDecoratorFactory
{
    protected static function typeMap(): array
    {
        return [
            'referral' => ReferralResourceDecorator::class,
        ];
    }
}
