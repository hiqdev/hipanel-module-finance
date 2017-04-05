<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

return [
    'aliases' => [
        '@bill' => '/finance/bill',
        '@purse' => '/finance/purse',
        '@tariff' => '/finance/tariff',
        '@pay' => '/merchant/pay',
        '@cart' => '/cart/cart',
        '@finance' => '/finance',
    ],
    'modules' => [
        'finance' => [
            'class' => \hipanel\modules\finance\Module::class,
        ],
        'cart' => [
            'class' => \hiqdev\yii2\cart\Module::class,
            'termsPage' => (isset($params['organizationUrl']) ? $params['organizationUrl'] : '/') . 'rules',
            'orderPage' => '/finance/cart/select',
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
                'on cartChange' => [\hipanel\modules\finance\cart\CartCalculator::class, 'handle'],
            ],
        ],
        'merchant' => [
            'class' => \hiqdev\yii2\merchant\Module::class,
            'returnPage' => '/finance/pay/return',
            'notifyPage' => '/finance/pay/notify',
            'finishPage' => '/finance/bill',
            'depositClass' => \hipanel\modules\finance\merchant\Deposit::class,
            'collectionClass' => \hipanel\modules\finance\merchant\Collection::class,
        ],
    ],
    'components' => [
        'urlManager' => [
            'rules' => [
                [
                    'pattern' => 'finance/purse/<id:\d+>/generate/monthly/<type:\w+>.<login:[.\@\w\d_]+>.<currency:\w+>.<month:[\d-]+>.pdf',
                    'route' => 'finance/purse/generate-monthly-document'
                ],
                [
                    'pattern' => 'finance/purse/<id:\d+>/generate/<type:\w+>.<login:[.\@\w\d_]+>.<currency:\w+>.pdf',
                    'route' => 'finance/purse/generate-document'
                ]
            ]
        ],
        'themeManager' => [
            'pathMap' => [
                '@hipanel/modules/finance/views' => '$themedViewPaths',
            ],
        ],
        'i18n' => [
            'translations' => [
                'hipanel:finance' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
                'hipanel:finance:change' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
                'hipanel:finance:tariff' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
                'hipanel:finance:tariff:types' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                    'forceTranslation' => true,
                ],
                'hipanel:finance:deposit' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            \hiqdev\thememanager\menus\AbstractSidebarMenu::class => [
                'add' => [
                    'finance' => [
                        'menu' => \hipanel\modules\finance\menus\SidebarMenu::class,
                        'where' => [
                            'after' => ['clients', 'dashboard'],
                            'before' => ['tickets', 'domains', 'servers', 'hosting'],
                        ],
                    ],
                ],
            ],
            \hiqdev\yii2\merchant\widgets\PayButton::class => [
                'class' => \hiqdev\yii2\merchant\widgets\PayButton::class,
                'as commentBehavior' => [
                    'class' => \hipanel\modules\finance\behaviors\PayButtonCommentBehavior::class,
                ]
            ],
        ],
        'singletons' => [
            hipanel\modules\finance\providers\BillTypesProvider::class => hipanel\modules\finance\providers\BillTypesProvider::class,
            hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface::class => hipanel\modules\finance\transaction\ApiTransactionRepository::class,
        ],
    ],
];
