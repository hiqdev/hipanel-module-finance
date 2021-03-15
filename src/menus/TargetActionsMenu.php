<?php

namespace hipanel\modules\finance\menus;

use hipanel\modules\finance\models\Target;
use hiqdev\yii2\menus\Menu;

class TargetActionsMenu extends Menu
{
    public Target $model;

    public function items()
    {
        return [];
    }
}
