<?php

namespace hipanel\modules\finance\menus;

use Yii;

class BillDetailMenu extends \hipanel\menus\AbstractDetailMenu
{
    public $model;

    public function items()
    {
        $actions = BillActionsMenu::create(['model' => $this->model])->items();
        $items = array_merge($actions, [
            'delete' => [
                'label' => Yii::t('hipanel', 'Delete'),
                'icon' => 'fa-trash',
                'url' => ['@bill/delete', 'id' => $this->model->id],
                'encode' => false,
                'linkOptions' => [
                    'data' => [
                        'confirm' => Yii::t('hipanel', 'Are you sure you want to delete this item?'),
                        'method' => 'POST',
                        'pjax' => '0',
                    ],
                ],
                'visible' => Yii::$app->user->can('bill.delete'),
            ],
        ]);
        unset($items['view']);

        return $items;
    }
}
