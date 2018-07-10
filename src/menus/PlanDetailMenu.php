<?php

namespace hipanel\modules\finance\menus;

use hipanel\modules\finance\models\Plan;
use hipanel\widgets\SettingsModal;
use Yii;

class PlanDetailMenu extends \hipanel\menus\AbstractDetailMenu
{
    /**
     * @var Plan
     */
    public $model;

    public function items()
    {
        $actions = PlanActionsMenu::create(['model' => $this->model])->items();
        $items = array_merge($actions, [
            'copy' => [
                'label' => SettingsModal::widget([
                    'model' => $this->model,
                    'title' => Yii::t('hipanel', 'Copy'),
                    'icon' => 'fa-copy fa-fw',
                    'scenario' => 'copy',
                    'handleSubmit' => false,
                ]),
                'encode' => false,
                'visible' => !$this->model->isDeleted(),
            ],
            'delete' => [
                'label' => Yii::t('hipanel', 'Delete'),
                'icon' => 'fa-trash',
                'url' => ['@plan/delete', 'id' => $this->model->id],
                'encode' => false,
                'linkOptions' => [
                    'data' => [
                        'confirm' => Yii::t('hipanel', 'Are you sure you want to delete this item?'),
                        'method' => 'POST',
                        'pjax' => '0',
                    ],
                ],
                'visible' => \count($this->model->sales) === 0 && !$this->model->isDeleted(),
            ],
            'restore' => [
                'label' => Yii::t('hipanel.finance.plan', 'Restore'),
                'icon' => 'fa-refresh',
                'url' => ['@plan/restore'],
                'linkOptions' => [
                    'data' => [
                        'method' => 'POST',
                        'pjax' => '0',
                        'params' => ['selection[]' => $this->model->id]
                    ],
                ],
                'visible' => $this->model->isDeleted(),
            ],
        ]);
        unset($items['view']);

        return $items;
    }
}
