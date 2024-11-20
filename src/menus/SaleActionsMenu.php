<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\menus;

use hipanel\modules\finance\models\Sale;
use hiqdev\yii2\menus\Menu;
use Yii;

class SaleActionsMenu extends Menu
{
    public Sale $model;

    public function items(): array
    {
        return [
            'update' => [
                'label' => Yii::t('hipanel:finance:sale', 'Edit'),
                'icon' => 'fa-pencil',
                'url' => ['@sale/update', 'id' => $this->model->id],
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
                'visible' => Yii::$app->user->can('sale.update') && $this->model->isOperateable(),
            ],
        ];
    }
}
