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

use Yii;

class SidebarMenu extends \hiqdev\yii2\menus\Menu
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
                        'label'   => Yii::t('hipanel:finance', 'Payments'),
                        'url'     => ['/finance/bill/index'],
                        'visible' => $user->can('bill.read'),
                    ],
                    'deposit' => [
                        'label'   => Yii::t('hipanel:finance', 'Recharge account'),
                        'url'     => ['/merchant/pay/deposit'],
                        'visible' => $user->can('deposit'),
                    ],
                    'tariffs' => [
                        'label'   => Yii::t('hipanel:finance', 'Tariffs'),
                        'url'     => ['/finance/tariff/index'],
                        'visible' => $user->can('plan.create'),
                    ],
                    'requisites' => [
                        'label'   => Yii::t('hipanel:finance', 'Requisites'),
                        'url'     => ['/finance/bill/requisites'],
                        'visible' => $user->can('manage'),
                    ],
                    'holds' => [
                        'label'   => Yii::t('hipanel:finance', 'Held payments'),
                        'url'     => ['/finance/held-payments/index'],
                        'visible' => $user->can('resell') && $user->can('bill.update'),
                    ],
                    'sale' => [
                        'label'   => Yii::t('hipanel:finance:sale', 'Sales'),
                        'url'     => ['/finance/sale/index'],
                        'visible' => $user->can('manage'),
                    ],
                    'generate' => [
                        'label'   => Yii::t('hipanel:finance', 'Generate documents'),
                        'url'     => ['/finance/purse/generate-all'],
                        'visible' => $user->can('document.generate-all'),
                    ],
                    'plans' => [
                        'label'   => Yii::t('hipanel:finance', 'Tariff plans'),
                        'url'     => ['@plan/index'],
                        'visible' => $user->can('test.beta'),
                    ],
                    'prices' => [
                        'label'   => Yii::t('hipanel:finance', 'Prices'),
                        'url'     => ['@price/index'],
                        'visible' => $user->can('test.beta'),
                    ],
                ],
            ],
        ];
    }
}
