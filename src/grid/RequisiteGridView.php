<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\helpers\Url;
use hipanel\modules\finance\menus\RequisiteActionsMenu;
use hiqdev\yii2\menus\grid\MenuColumn;
use hipanel\modules\client\grid\ContactGridView;
use hipanel\grid\XEditableColumn;

class RequisiteGridView extends ContactGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'serie' => [
                'class' => XEditableColumn::class,
                'pluginOptions' => [
                    'url' => Url::to('@requisite/set-serie'),
                ],
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => RequisiteActionsMenu::class,
            ],
        ]);
    }
}
