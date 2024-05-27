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

use hipanel\modules\finance\models\decorators\ResourceDecoratorFactory;
use hipanel\modules\finance\models\decorators\target\CdnTrafficResourceDecorator;
use hipanel\modules\finance\models\decorators\target\CdnTrafficPlainResourceDecorator;
use hipanel\modules\finance\models\decorators\target\CdnTrafficSSLResourceDecorator;
use hipanel\modules\finance\models\decorators\target\CdnTrafficMaxResourceDecorator;

class ServerResourceDecoratorFactory extends ResourceDecoratorFactory
{
    protected static function typeMap(): array
    {
        return [
            'backup_du' => BackupResourceDecorator::class,
            'chassis' => ChassisResourceDecorator::class,
            'cpu' => CpuResourceDecorator::class,
            'hdd' => HddResourceDecorator::class,
            'ip_num' => IpResourceDecorator::class,
            'isp5' => Isp5ResourceDecorator::class,
            'isp' => IspResourceDecorator::class,
            'ram' => RamResourceDecorator::class,
            'speed' => SpeedResourceDecorator::class,
            'panel' => PanelResourceDecorator::class,
            'support_time' => SupportResourceDecorator::class,
            'server_traf95_max' => Traffic95ResourceDecorator::class,
            'server_traf95_in' => Traffic95ResourceDecorator::class,
            'server_traf95' => Traffic95ResourceDecorator::class,
            'server_traf_max' => TrafficResourceDecorator::class,
            'server_traf_in' => TrafficResourceDecorator::class,
            'server_traf' => TrafficResourceDecorator::class,
            'server_du' => ServerDUResourceDecorator::class,
            'storage_du' => StorageDUResourceDecorator::class,
            'server_ssd' => ServerSSDResourceDecorator::class,
            'server_sata' => ServerSataDUResourceDecorator::class,
            'server_files' => ServerFilesResourceDecorator::class,
            'rack_unit' => RackUnitResourceDecorator::class,
            'location' => LocationResourceDecorator::class,
            'power' => PowerResourceDecorator::class,
            'monthly' => MonthlyResourceDecorator::class,
            'lb_capacity_unit' => LoadBalancerResourceDecorator::class,
            'lb_ha_capacity_unit' => HALoadBalancerResourceDecorator::class,
            'cdn_traf' => CdnTrafficResourceDecorator::class,
            'cdn_traf_plain' => CdnTrafficPlainResourceDecorator::class,
            'cdn_traf_ssl' => CdnTrafficSSLResourceDecorator::class,
            'cdn_traf_max' => CdnTrafficMaxResourceDecorator::class,
        ];
    }
}
