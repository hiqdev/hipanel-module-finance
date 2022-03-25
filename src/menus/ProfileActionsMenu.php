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

use hipanel\menus\AbstractDetailMenu;
use hipanel\widgets\ModalButton;
use Yii;
use yii\helpers\Html;

class ProfileActionsMenu extends AbstractDetailMenu
{
    public $model;

    public function items()
    {
        return [
            [
                'label' => Yii::t('hipanel', 'Update'),
                'icon' => 'fa-pencil',
                'url' => ['@tariffprofile/update', 'id' => $this->model->id],
                'visible' => Yii::$app->user->can('plan.update'),
            ],
            [
                'label' => ModalButton::widget([
                    'model' => $this->model,
                    'scenario' => 'delete',
                    'button' => ['label' => '<i class="fa fa-fw fa-trash-o"></i>' . Yii::t('hipanel', 'Delete')],
                    'body' => Yii::t('hipanel.finance.tariffprofile', 'Tariff must be unlinked form all objects before. Are you sure you want to delete tariffprofile {name}?', ['name' => $this->model->name]),
                    'modal' => [
                        'header' => Html::tag('h4', Yii::t('hipanel:finance:tariff', 'Confirm tariff profile deleting')),
                        'headerOptions' => ['class' => 'label-danger'],
                        'footer' => [
                            'label' => Yii::t('hipanel.finance.tariffprofile', 'Delete tariff profile'),
                            'data-loading-text' => Yii::t('hipanel.finance.tariffprofile', 'Deleting tariff profile...'),
                            'class' => 'btn btn-danger',
                        ],
                    ],
                ]),
                'visible' => Yii::$app->user->can('plan.delete') && !$this->model->isDefault(),
                'encode' => false,
            ],
            'view' => [
                'label' => Yii::t('hipanel', 'View'),
                'icon' => 'fa-info',
                'url' => ['@tariffprofile/view', 'id' => $this->model->id],
                'encode' => false,
                'visible' => Yii::$app->user->can('plan.read'),
            ],
        ];
    }
}
