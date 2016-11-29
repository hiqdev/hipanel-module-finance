<?php

namespace hipanel\modules\finance\menus;

class TariffDetailMenu extends \hiqdev\menumanager\Menu
{
    public $model;

    public function items()
    {
        $actions = TariffActionsMenu::create(['model' => $this->model])->items();
        $items = array_merge($actions, []);

        return $items;
    }
}
