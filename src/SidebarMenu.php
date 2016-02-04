<?php

/*
 * Finance Plugin for HiPanel
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
        return [
            'finance' => [
                'label' => Yii::t('app', 'Finance'),
                'url'   => ['/finance/bill/index'],
                'icon'  => 'fa-dollar',
                'items' => [
                    'payments' => [
                        'label' => Yii::t('app', 'Payments'),
                        'url'   => ['/finance/bill/index'],
                    ],
                    'deposit' => [
                        'label' => Yii::t('app', 'Recharge account'),
                        'url'   => ['/merchant/pay/deposit'],
                    ],
                    'tariffs' => [
                        'label'   => Yii::t('app', 'Tariffs'),
                        'url'     => ['/finance/tariff/index'],
                        'visible' => function () { return Yii::$app->user->can('support') ?: false; },
                    ],
                ],
            ],
        ];
    }
}
