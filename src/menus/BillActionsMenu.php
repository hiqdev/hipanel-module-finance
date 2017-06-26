<?php

namespace hipanel\modules\finance\menus;

use Yii;

class BillActionsMenu extends \hiqdev\yii2\menus\Menu
{
    public $model;

    public function items()
    {
        return [
            'view' => [
                'label' => Yii::t('hipanel', 'View'),
                'icon' => 'fa-info',
                'url' => ['@bill/view', 'id' => $this->model->id],
            ],
            'copy' => [
                'label' => Yii::t('hipanel', 'Copy'),
                'icon' => 'fa-copy',
                'url' => ['@bill/copy', 'id' => $this->model->id],
                'visible' => Yii::$app->user->can('bill.create'),
            ],
            'update' => [
                'label' => Yii::t('hipanel', 'Update'),
                'icon' => 'fa-pencil',
                'url' => ['@bill/update', 'id' => $this->model->id],
                'visible' => Yii::$app->user->can('bill.update'),
            ],
        ];
    }
}
