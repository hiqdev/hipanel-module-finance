<?php

namespace hipanel\modules\finance\menus;

use hipanel\helpers\Url;
use hipanel\menus\AbstractDetailMenu;
use hipanel\modules\finance\models\Target;
use hipanel\widgets\AjaxModalWithTemplatedButton;
use Yii;
use yii\bootstrap\Modal;
use yii\helpers\Html;

class TargetDetailMenu extends AbstractDetailMenu
{
    public Target $model;

    public function items()
    {
        $items = TargetActionsMenu::create([
            'model' => $this->model,
        ])->items();
        $hasSales = count($this->model->sales) > 0;
        $hasActiveSale = $this->model->getActiveSale() !== null;

        $items = array_merge($items, [
            [
                'label' => AjaxModalWithTemplatedButton::widget([
                    'ajaxModalOptions' => [
                        'bulkPage' => false,
                        'usePost' => false,
                        'id' => 'target-change-plan',
                        'scenario' => 'change-plan',
                        'actionUrl' => ['change-plan', 'id' => $this->model->id],
                        'handleSubmit' => Url::toRoute('change-plan', ['id' => $this->model->id]),
                        'size' => Modal::SIZE_SMALL,
                        'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Change plan'), ['class' => 'modal-title']),
                        'toggleButton' => [
                            'tag' => 'a',
                            'label' => Yii::t('hipanel:finance', 'Change plan') . '<span class="pull-right"><i class="fa fa-fw fa-pencil"></i></span>',
                            'class' => 'clickable',
                        ],
                    ],
                    'toggleButtonTemplate' => '<li>{toggleButton}</li>',
                ]),
                'visible' => $hasActiveSale && !$this->model->isDeleted(),
                'encode' => false,
            ],
            [
                'label' => AjaxModalWithTemplatedButton::widget([
                    'ajaxModalOptions' => [
                        'bulkPage' => false,
                        'usePost' => false,
                        'id' => 'target-sale',
                        'scenario' => 'sale',
                        'actionUrl' => ['sale', 'id' => $this->model->id],
                        'handleSubmit' => Url::toRoute('sale', ['id' => $this->model->id]),
                        'size' => Modal::SIZE_SMALL,
                        'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Sale'), ['class' => 'modal-title']),
                        'toggleButton' => [
                            'tag' => 'a',
                            'label' => Yii::t('hipanel:finance', 'Sale') . '<span class="pull-right"><i class="fa fa-fw fa-share"></i></span>',
                            'class' => 'clickable',
                        ],
                    ],
                    'toggleButtonTemplate' => '<li>{toggleButton}</li>',
                ]),
                'visible' => !$hasSales && !$this->model->isDeleted(),
                'encode' => false,
            ],
            [
                'label' => AjaxModalWithTemplatedButton::widget([
                    'ajaxModalOptions' => [
                        'bulkPage' => false,
                        'usePost' => false,
                        'id' => 'target-close-sale',
                        'scenario' => 'close-sale',
                        'actionUrl' => ['close-sale', 'id' => $this->model->id],
                        'handleSubmit' => Url::toRoute('close-sale', ['id' => $this->model->id]),
                        'size' => Modal::SIZE_SMALL,
                        'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Close sale'), ['class' => 'modal-title']),
                        'toggleButton' => [
                            'tag' => 'a',
                            'label' => Yii::t('hipanel:finance', 'Close sale') . '<span class="pull-right text-danger"><i class="fa fa-fw fa-times"></i></span>',
                            'class' => 'clickable',
                        ],
                    ],
                    'toggleButtonTemplate' => '<li>{toggleButton}</li>',
                ]),
                'visible' => $hasActiveSale && !$this->model->isDeleted(),
                'encode' => false,
            ],
        ]);

        unset($items['view']);

        return $items;
    }
}
