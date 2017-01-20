<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\menus;

use hipanel\widgets\ModalButton;
use Yii;
use yii\helpers\Html;

class TariffActionsMenu extends \hiqdev\yii2\menus\Menu
{
    public $model;

    public function items()
    {
        return [
            [
                'label' => Yii::t('hipanel', 'Update'),
                'icon' => 'fa-pencil',
                'url' => ['@tariff/update', 'id' => $this->model->id],
            ],
            [
                'label' => ModalButton::widget([
                    'model' => $this->model->getTariff(),
                    'scenario' => 'delete',
                    'button' => ['label' => '<i class="fa fa-fw fa-trash-o"></i>' . Yii::t('hipanel', 'Delete')],
                    'body' => Yii::t('hipanel:finance:tariff', 'Tariff must be unlinked form all objects before. Are you sure you want to delete tariff {name}?', ['name' => $model->name]),
                    'modal' => [
                        'header' => Html::tag('h4', Yii::t('hipanel:finance:tariff', 'Confirm tariff deleting')),
                        'headerOptions' => ['class' => 'label-danger'],
                        'footer' => [
                            'label' => Yii::t('hipanel:finance:tariff', 'Delete tariff'),
                            'data-loading-text' => Yii::t('hipanel:finance:tariff', 'Deleting tariff...'),
                            'class' => 'btn btn-danger',
                        ],
                    ],
                ]),
                'encode' => false,
            ],
        ];
    }
}
