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
use hipanel\modules\finance\models\InstallmentPlan;
use hipanel\widgets\AuditButton;
use Yii;

class InstallmentPlanDetailMenu extends AbstractDetailMenu
{
    public InstallmentPlan $model;

    public function items(): array
    {
        return [
            [
                'label' => '<i class="fa fa-trash fa-fw pull-right"></i>' . Yii::t('hipanel', 'Delete'),
                'url' => ['@installment-plan/delete', 'id' => $this->model->id],
                'encode' => false,
                'visible' => Yii::$app->user->can('installment-plan.delete') && !$this->model->isDeleted(),
                'linkOptions' => [
                    'data' => [
                        'method' => 'post',
                        'pjax' => '0',
                        'confirm' => Yii::t('hipanel:finance', 'Are you sure you want to delete this installment plan?'),
                        'params' => [
                            'InstallmentPlan[id]' => $this->model->id,
                        ],
                    ],
                ],
            ],
            [
                'label' => '<i class="fa fa-undo fa-fw pull-right"></i>' . Yii::t('hipanel', 'Restore'),
                'url' => ['@installment-plan/restore', 'id' => $this->model->id],
                'encode' => false,
                'visible' => Yii::$app->user->can('installment-plan.restore') && $this->model->isDeleted(),
                'linkOptions' => [
                    'data' => [
                        'method' => 'post',
                        'pjax' => '0',
                        'params' => [
                            'InstallmentPlan[id]' => $this->model->id,
                        ],
                    ],
                ],
            ],
            [
                'label' => AuditButton::widget(['model' => $this->model, 'rightIcon' => true]),
                'encode' => false,
            ],
        ];
    }
}
