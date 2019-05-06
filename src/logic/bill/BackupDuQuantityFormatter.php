<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class BackupDuQuantityFormatter extends DefaultQuantityFormatter
{
    public function format(): string
    {
        $quantity = $this->getQuantity();
        return Yii::t('hiqdev.units', '{quantity, number} {unit}', [
            'quantity' => $quantity->getQuantity(),
            'unit' => (function (string $unitName) {
                switch ($unitName) {
                    case 'b':
                        return Yii::t('hiqdev.units', 'B');
                    case 'kb':
                        return Yii::t('hiqdev.units', 'kB');
                    case 'mb':
                        return Yii::t('hiqdev.units', 'MB');
                    case 'gb':
                        return Yii::t('hiqdev.units', 'GB');
                    case 'tb':
                        return Yii::t('hiqdev.units', 'TB');
                    case 'pb':
                        return Yii::t('hiqdev.units', 'PB');
                }

                return Yii::t('hiqdev.units', '');
            })($quantity->getUnit()->getName())
        ]);
    }
}
