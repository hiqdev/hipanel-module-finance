<?php
/**
 * Client module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-client
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\menus\RequisiteActionsMenu;
use hiqdev\yii2\menus\grid\MenuColumn;
use hipanel\modules\client\grid\ContactGridView;

class RequisiteGridView extends ContactGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => RequisiteActionsMenu::class,
            ],
        ]);
    }
}
