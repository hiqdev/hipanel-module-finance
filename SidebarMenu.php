<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance;

class SidebarMenu extends \hipanel\base\Menu implements \yii\base\BootstrapInterface
{

    protected $_addTo = 'sidebar';

    protected $_where = [
        'after'     => ['clients', 'dashboard', 'header'],
        'before'    => ['tickets', 'domains', 'servers', 'hosting'],
    ];

    protected $_items = [
        'finance' => [
            'label' => 'Finance',
            'url'   => ['/finance/bill/index'],
            'icon'  => 'fa-dollar',
            'items' => [
                'payments' => [
                    'label' => 'Payments',
                    'url'   => ['/finance/bill/index'],
                    'icon'  => 'fa-money',
                ],
                'deposit' => [
                    'label' => 'Recharge account',
                    'url'   => ['/finance/bill/deposit'],
                    'icon'  => 'fa-credit-card',
                ],
                'tariffs' => [
                    'label' => 'Tariffs',
                    'url'   => ['/finance/tariff/index'],
                    'icon'  => 'fa-circle-o',
                ],
            ],
        ],
    ];

}
