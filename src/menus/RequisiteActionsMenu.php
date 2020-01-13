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

use hipanel\widgets\AjaxModalWithTemplatedButton;
use hiqdev\yii2\menus\Menu;
use yii\bootstrap\Modal;
use yii\helpers\Html;

use Yii;

class RequisiteActionsMenu extends Menu
{
    public $model;

    public function items()
    {
        return [
            'view' => [
                'label' => Yii::t('hipanel', 'View'),
                'icon' => 'fa-info',
                'url' => ['@requisite/view', 'id' => $this->model->id],
                'encode' => false,
            ],
            'edit' => [
                'label' => Yii::t('hipanel', 'Edit'),
                'icon' => 'fa-pencil',
                'url' => ['@contact/update', 'id' => $this->model->id],
                'encode' => false,
            ],
            [
                'label' => AjaxModalWithTemplatedButton::widget([
                    'ajaxModalOptions' => [
                        'id' => 'reserve-number-modal-' . $this->model->id,
                        'bulkPage' => false,
                        'header' => Html::tag('h4', $item['label'], ['class' => 'modal-title']),
                        'scenario' => 'default',
                        'actionUrl' => ['reserve-number', 'id' => $this->model->id],
                        'size' => Modal::SIZE_LARGE,
                        'handleSubmit' => ['reserve-number', 'id' => $this->model->id],
                        'toggleButton' => [
                            'tag' => 'a',
                            'label' => Html::tag('i', null, ['class' => 'fa fa-ticket']) . " " . Yii::t('hipanel:client', 'Reserve number'),
                            'style' => 'cursor: pointer;',
                        ],
                    ],
                    'toggleButtonTemplate' => '{toggleButton}',
                ]),
                'encode' => false,
                'visible' => Yii::$app->user->can('requisites.update') && $this->model->isRequisite(),
            ],
        ];
    }
}
