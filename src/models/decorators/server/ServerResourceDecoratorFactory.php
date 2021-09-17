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
            'server_ssd' => HddResourceDecorator::class,
            'server_sata' => ServerSataDUResourceDecorator::class,
            'rack_unit' => RackUnitResourceDecorator::class,
            'location' => LocationResourceDecorator::class,
            'monthly' => MonthlyResourceDecorator::class,
        ];
    }
}
