<?php

namespace hipanel\modules\finance\logic\bill;

use yii\base\Model;
use Yii;

/**
 * Class BillQuantityFactory
 * @package hipanel\modules\finance\logic\bill
 */
class BillQuantityFactory
{
    protected $types = [
        'support_time' => 'SupportTimeQuantity',
        'server_traf_max' => 'ServerTrafMaxQuantity',
        'backup_du' => 'BackupDuQuantity',
        'ip_num' => 'IPNumQuantity',
        'monthly' => 'MonthlyQuantity',
    ];

    /**
     * @param $type
     * @param $model Model
     * @return Object
     */
    public function createByType(string $type, Model $model)
    {
        $className = static::buildClassName($type);

        return Yii::createObject([
            'class' => $className,
            'model' => $model,
        ]);
    }

    /**
     * @param string $type Tariff type
     * @return string
     */
    protected function buildClassName($type)
    {
        return 'hipanel\modules\finance\logic\bill\\' . $this->types[$type];
    }
}
