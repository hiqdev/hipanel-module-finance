<?php

declare(strict_types=1);

namespace hipanel\modules\finance\menus;

use Yii;
use hiqdev\yii2\menus\Menu;
use hipanel\modules\finance\models\Target;

class TargetActionsMenu extends Menu
{
    public Target $model;

    public function items()
    {
        $user = Yii::$app->user;

        return [
            'update' => [
                'label' => Yii::t('hipanel', 'Update'),
                'icon' => 'fa-pencil-square-o',
                'url' => ['@target/update', 'id' => $this->model->id],
                'visible' => $user->can('plan.update'),
            ],
            'delete' => [
                'label' => Yii::t('hipanel', 'Delete'),
                'icon' => 'fa-trash',
                'url' => ['@target/delete', 'id' => $this->model->id],
                'encode' => false,
                'visible' => $user->can('plan.delete'),
                'linkOptions' => [
                    'data' => [
                        'confirm' => Yii::t('hipanel', 'Are you sure you want to delete this target?'),
                        'method' => 'POST',
                        'pjax' => '0',
                    ],
                ],
            ],
        ];
    }
}
