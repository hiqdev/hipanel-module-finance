<?php

namespace hipanel\modules\finance\menus;

use hipanel\menus\AbstractDetailMenu;
use hipanel\modules\finance\models\Target;

class TargetDetailMenu extends AbstractDetailMenu
{
    public Target $model;

    public function items()
    {
        $items = TargetActionsMenu::create([
            'model' => $this->model,
        ])->items();

        $items = array_merge($items, []);

        unset($items['view']);

        return $items;
    }
}
