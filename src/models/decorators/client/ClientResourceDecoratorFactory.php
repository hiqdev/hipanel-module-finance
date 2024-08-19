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
use hipanel\modules\finance\models\decorators\target\IpPublicResourceDecorator;
use hipanel\modules\finance\models\decorators\target\IpRegularResourceDecorator;
use hipanel\modules\finance\models\decorators\target\IpAnycastResourceDecorator;
use hipanel\modules\finance\models\decorators\server\SupportResourceDecorator;

class ClientResourceDecoratorFactory extends ResourceDecoratorFactory
{
    protected static function typeMap(): array
    {
        return [
            'referral' => ReferralResourceDecorator::class,
            'support_time' => SupportResourceDecorator::class,
            'cloud_ip_regular' => IpRegularResourceDecorator::class,
            'cloud_ip_public' => IpPublicResourceDecorator::class,
            'cloud_ip_anycast' => IpAnycastResourceDecorator::class,
            'cloud_ip_regular_max' => IpRegularResourceDecorator::class,
            'cloud_ip_public_max' => IpPublicResourceDecorator::class,
            'cloud_ip_anycast_max' => IpAnycastResourceDecorator::class,
        ];
    }
}
