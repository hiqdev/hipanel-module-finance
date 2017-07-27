<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

/**
 * Class BillQuantityFactory
 * @package hipanel\modules\finance\logic\bill
 */
class BillQuantityFactory implements BillQuantityFactoryInterface
{
    protected $types = [
        'support_time' => 'SupportTimeQuantity',
        'server_traf_max' => 'ServerTrafMaxQuantity',
        'backup_du' => 'BackupDuQuantity',
        'ip_num' => 'IPNumQuantity',
        'monthly' => 'MonthlyQuantity',
        'drenewal' => 'DomainRenewalQuantity',
    ];

    protected $type;

    /**
     * @param $type
     * @param $model
     * @return Object|null
     */
    public function createByType(string $type, $model)
    {
        $this->fixType($type);
        if (in_array($this->type, array_keys($this->types))) {
            $className = static::buildClassName($this->type);

            return Yii::createObject([
                'class' => $className,
                'model' => $model,
            ]);
        }

        return null;
    }

    /**
     * @param string $type Tariff type
     * @return string
     */
    protected function buildClassName()
    {
        return 'hipanel\modules\finance\logic\bill\\' . $this->types[$this->type];
    }

    protected function fixType($type)
    {
        if (strpos($type, ',') !== false) {
            $type = end(explode(',', $type));
        }
        $this->type = $type;
    }

    public function create($model)
    {
        return $this->createByType($model->type, $model);
    }
}
