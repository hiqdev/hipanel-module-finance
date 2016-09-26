<?php

namespace hipanel\modules\finance\models\decorators\server;

use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use yii\base\InvalidConfigException;

class ServerResourceDecoratorFactory
{
    private static function typeMap()
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
            'server_traf_max' => TrafficResourceDecorator::class,
            'location' => LocationResourceDecorator::class,
        ];
    }

    /**
     * @param $resource
     * @return ResourceDecoratorInterface
     * @throws InvalidConfigException
     */
    public static function createFromResource($resource)
    {
        $type = $resource->model_type ?: $resource->type;
        $map = self::typeMap();

        if (!isset($map[$type])) {
            throw new InvalidConfigException('No representative decoration class found for type "' . $type . '"');
        }

        return new $map[$type]($resource);
    }
}
