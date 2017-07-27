<?php

namespace hipanel\modules\finance\logic\bill;

use Yii;

class BackupDuQuantity extends AbstractBillQuantity
{
    public function getText()
    {
        return Yii::$app->formatter->asShortSize($this->model->quantity * 1024 * 1024 * 1024);
    }
}
