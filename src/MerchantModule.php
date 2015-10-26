<?php

namespace hipanel\modules\finance;

use Yii;

class MerchantModule extends \hiqdev\yii2\merchant\Module
{
    public $controllerNamespace = 'hiqdev\yii2\merchant\controllers';

    public function init()
    {
        parent::init();
        $this->setViewPath('@hiqdev\yii2\merchant\views');
    }
}
