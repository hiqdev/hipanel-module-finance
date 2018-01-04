<?php

namespace hipanel\modules\finance\menus;

use Yii;

class PriceActionsMenu extends \hiqdev\yii2\menus\Menu
{
    public $model;

    public function items()
    {
        return [
            'view' => [
                'label' => Yii::t('hipanel', 'View'),
                'icon' => 'fa-info',
                'url' => ['@price/view', 'id' => $this->model->id],
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
            ],
            'update' => [
                'label' => Yii::t('hipanel', 'Update'),
                'icon' => 'fa-pencil',
                'url' => ['@price/update', 'id' => $this->model->id],
                'visible' => true,
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
            ],
        ];
    }
}
