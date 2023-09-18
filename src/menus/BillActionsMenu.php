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
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
            ],
            'copy' => [
                'label' => Yii::t('hipanel', 'Copy'),
                'icon' => 'fa-copy',
                'url' => ['@bill/copy', 'id' => $this->model->id],
                'visible' => $this->model->canCopy(),
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
            ],
            'copy-with-zero-sums' => [
                'label' => Yii::t('hipanel', 'Copy with zero sums'),
                'icon' => 'fa-clone',
                'url' => ['@bill/copy-with-zero-sums', 'id' => $this->model->id],
                'visible' => $this->model->canCopy(),
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
            ],
            'update' => [
                'label' => Yii::t('hipanel', 'Update'),
                'icon' => 'fa-pencil',
                'url' => ['@bill/update', 'id' => $this->model->id],
                'visible' => $this->model->canEdit(),
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
            ],
            [
                'label' => Yii::t('hipanel:finance', 'Charges'),
                'icon' => 'fa-th-list',
                'url' => Url::toSearch('charge', ['bill_id_in' => $this->model->id]),
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
                'visible' => Yii::$app->user->can('bill.charges.read'),
            ],
        ];
    }
}
