<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

return [
    'aliases' => [
        '@bill'   => '/finance/bill',
        '@purse'  => '/finance/purse',
        '@tariff' => '/finance/tariff',
        '@pay'    => '/merchant/pay',
        '@cart'   => '/cart/cart',
        '@finance'=> '/finance',
    ],
    'modules' => [
        'finance' => [
            'class' => \hipanel\modules\finance\Module::class,
        ],
        'cart' => [
            'class'          => \hiqdev\yii2\cart\Module::class,
            'termsPage'      => $params['orgUrl'] . 'rules',
            'orderPage'      => '/finance/cart/select',
            /*'orderButton'    => function ($module) {
                return Yii::$app->getView()->render('@hipanel/modules/finance/views/cart/order-button', [
                    'module' => $module,
                ]);
            },*/
            'paymentMethods' => function () {
                return Yii::$app->getView()->render('@hipanel/modules/finance/views/cart/payment-methods', [
                    'merchants' => Yii::$app->getModule('merchant')->getCollection([])->getItems(),
                ]);
            },
            'shoppingCartOptions' => [
                'on cartChange' => [\hipanel\modules\finance\cart\CartCalculator::class, 'execute'],
            ],
        ],
        'merchant' => [
            'class'           => \hiqdev\yii2\merchant\Module::class,
            'returnPage'      => '/finance/pay/return',
            'notifyPage'      => '/finance/pay/notify',
            'finishPage'      => '/finance/bill',
            'depositClass'    => \hipanel\modules\finance\merchant\Deposit::class,
            'collectionClass' => \hipanel\modules\finance\merchant\Collection::class,
        ],
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'hipanel/finance' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                    'fileMap' => [
                        'hipanel/finance' => 'finance.php',
                    ],
                ],
                'hipanel/finance/*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                    'fileMap' => [
                        'hipanel/finance/change' => 'change.php',
                        'hipanel/finance/tariff' => 'tariff.php',
                    ],
                ],
            ],
        ],
        'menuManager' => [
            'items' => [
                'sidebar' => [
                    'add' => [
                        'finance' => [
                            'menu' => \hipanel\modules\finance\menus\SidebarFinanceMenu::class,
                            'where' => [
                                'after'  => ['clients', 'dashboard'],
                                'before' => ['tickets', 'domains', 'servers', 'hosting'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
