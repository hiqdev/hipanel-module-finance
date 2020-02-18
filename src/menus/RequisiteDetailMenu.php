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

use hipanel\helpers\Url;
use hipanel\widgets\AjaxModal;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use Yii;

class RequisiteDetailMenu extends \hipanel\menus\AbstractDetailMenu
{
    public $model;

    public function items()
    {
        $user = Yii::$app->user;
        $items = RequisiteActionsMenu::create([
            'model' => $this->model,
        ])->items();

        $items = array_merge($items, [
            [
                'label' => AjaxModal::widget([
                    'id' => 'set-templates-modal',
                    'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Set templates') . ': ' . Html::tag('b', "{$this->model->name} / {$this->model->organization}"), ['class' => 'modal-title']),
                    'scenario' => 'set-templates',
                    'actionUrl' => ['set-templates-modal', 'id' => $this->model->id],
                    'size' => Modal::SIZE_LARGE,
                    'toggleButton' => [
                         'label' => '<i class="fa fa-fw fa-exchange"></i>' . Yii::t('hipanel:finance', 'Set templates'),
                         'class' => 'clickable',
                         'data-pjax' => 0,
                         'tag' => 'a',
                    ],
                ]),
                'encode' => false,
                'visible' => $user->can('requisites.update'),
            ],
        ]);

        if (Yii::getAlias('@document', false)) {
            $items[] = [
                'label' => Yii::t('hipanel:client', 'Documents'),
                'icon' => 'fa-paperclip',
                'url' => ['@contact/attach-documents', 'id' => $this->model->id],
            ];
        }

        unset($items['view']);

        return $items;
    }
}
