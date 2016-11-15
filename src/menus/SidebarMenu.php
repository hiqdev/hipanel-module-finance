<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\menus;

use Yii;

class SidebarMenu extends \hiqdev\menumanager\Menu
{
    public function items()
    {
        $user = Yii::$app->user;
        if (!$user->can('manage') && !$user->can('deposit')) {
            return [];
        }

        return [
            'finance' => [
                'label' => Yii::t('hipanel:finance', 'Finance'),
                'url'   => ['/finance/bill/index'],
                'icon'  => 'fa-dollar',
                'items' => [
                    'payments' => [
                        'label' => Yii::t('hipanel:finance', 'Payments'),
                        'url'   => ['/finance/bill/index'],
                    ],
                    'deposit' => [
                        'label' => Yii::t('hipanel:finance', 'Recharge account'),
                        'url'   => ['/merchant/pay/deposit'],
                        'visible' => $user->can('deposit'),
                    ],
                    'tariffs' => [
                        'label'   => Yii::t('hipanel:finance', 'Tariffs'),
                        'url'     => ['/finance/tariff/index'],
                        'visible' => $user->can('manage'),
                    ],
                    'holds' => [
                        'label'   => Yii::t('hipanel:finance', 'Held payments'),
                        'url'     => ['/finance/held-payments/index'],
                        'visible' => Yii::$app->user->can('resell'),
                    ],
                ],
            ],
        ];
    }
}
