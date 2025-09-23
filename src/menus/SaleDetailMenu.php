<?php

declare(strict_types=1);


namespace hipanel\modules\finance\menus;

use hipanel\modules\finance\models\Sale;
use hipanel\widgets\AuditButton;
use Yii;

class SaleDetailMenu extends \hipanel\menus\AbstractDetailMenu
{
    public Sale $model;

    public function items(): array
    {
        $actions = SaleActionsMenu::create(['model' => $this->model])->items();

        return array_merge($actions, [
            'delete' => [
                'label' => Yii::t('hipanel', 'Delete'),
                'icon' => 'fa-trash',
                'url' => ['@sale/delete', 'id' => $this->model->id],
                'encode' => false,
                'visible' => Yii::$app->user->can('sale.delete'),
                'linkOptions' => [
                    'data' => [
                        'confirm' => Yii::t('hipanel', 'Are you sure you want to delete this item?'),
                        'method' => 'POST',
                        'pjax' => '0',
                    ],
                ],
            ],
            [
                'label' => AuditButton::widget(['model' => $this->model, 'rightIcon' => true]),
                'encode' => false,
            ],
        ]);
    }
}
