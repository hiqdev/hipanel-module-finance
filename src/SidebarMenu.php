<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance;

use Yii;

class SidebarMenu extends \hipanel\base\Menu implements \yii\base\BootstrapInterface
{
    protected $_addTo = 'sidebar';

    protected $_where = [
        'after'  => ['clients', 'dashboard', 'header'],
        'before' => ['tickets', 'domains', 'servers', 'hosting'],
    ];

    public function items()
    {
        return Yii::$app->user->can('support') && !Yii::$app->user->can('manage') ? [] : [
            'finance' => [
                'label' => Yii::t('hipanel/finance', 'Finance'),
                'url'   => ['/finance/bill/index'],
                'icon'  => 'fa-dollar',
                'items' => [
                    'payments' => [
                        'label' => Yii::t('hipanel/finance', 'Payments'),
                        'url'   => ['/finance/bill/index'],
                    ],
                    'deposit' => [
                        'label' => Yii::t('hipanel/finance', 'Recharge account'),
                        'url'   => ['/merchant/pay/deposit'],
                    ],
                    'tariffs' => [
                        'label'   => Yii::t('hipanel/finance', 'Tariffs'),
                        'url'     => ['/finance/tariff/index'],
                        'visible' => function () { return Yii::$app->user->can('support') ?: false; },
                    ],
                ],
            ],
        ];
    }
}
