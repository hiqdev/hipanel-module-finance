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
        '@sale' => '/finance/sale',
        '@pay' => '/merchant/pay',
        '@cart' => '/cart/cart',
        '@finance' => '/finance',
        '@plan' => '/finance/plan',
        '@price' => '/finance/price',
    ],
    'modules' => [
        'finance' => [
            'class' => \hipanel\modules\finance\Module::class,
        ],
        'cart' => array_filter([
            'class' => \hiqdev\yii2\cart\Module::class,
            'termsPage' => $params['organization.termsUrl'] ?: $params['organization.url'],
            'orderPage' => '/finance/cart/select',
            /*'orderButton'    => function ($module) {
                return Yii::$app->getView()->render('@hipanel/modules/finance/views/cart/order-button', [
                    'module' => $module,
                ]);
            },*/
            'paymentMethods' => function () {
                $merchants = Yii::$app->getModule('merchant')->getPurchaseRequestCollection(
                    new \hiqdev\yii2\merchant\models\DepositRequest(['amount' => 5])
                )->getItems();

                return Yii::$app->getView()->render('@hipanel/modules/finance/views/cart/payment-methods', [
                    'merchants' => $merchants,
                ]);
            },
            'shoppingCartOptions' => [
                'on cartChange' => [\hipanel\modules\finance\cart\CartCalculator::class, 'handle'],
                'session' => \yii\di\Instance::of(\hipanel\modules\finance\cart\storage\CartStorageInterface::class),
            ],
        ]),
        'merchant' => [
            'class' => \hiqdev\yii2\merchant\Module::class,
            'returnPage' => '/finance/pay/return',
            'notifyPage' => '/finance/pay/notify',
            'finishPage' => '/finance/bill',
            'purchaseRequestCollectionClass' => \hipanel\modules\finance\merchant\PurchaseRequestCollection::class,
        ],
    ],
    'components' => [
        'urlManager' => [
            'rules' => [
                [
                    'pattern' => 'finance/purse/<id:\d+>/generate/monthly/<type:\w+>.<login:[.\@\w\d_]+>.<currency:\w+>.<month:[\d-]+>.pdf',
                    'route' => 'finance/purse/generate-monthly-document',
                ],
                [
                    'pattern' => 'finance/purse/<id:\d+>/generate/<type:\w+>.<login:[.\@\w\d_]+>.<currency:\w+>.pdf',
                    'route' => 'finance/purse/generate-document',
                ],
            ],
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
                'hipanel.finance.units' => [
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
                'hipanel:finance:sale' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
                'hipanel.finance.suggestionTypes' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
                'hipanel.finance.price' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
                'hipanel.finance.priceTypes' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
                'hipanel.finance.plan' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@hipanel/modules/finance/messages',
                ],
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            \hipanel\modules\dashboard\menus\DashboardMenu::class => [
                'add' => [
                    'finance' => [
                        'menu' => [
                            'class' => \hipanel\modules\finance\menus\DashboardItem::class,
                        ],
                        'where' => [
                            'after' => ['clients', 'dashboard'],
                            'before' => ['tickets', 'domains', 'servers', 'hosting'],
                        ],
                    ],
                ],
            ],
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
                'action' => ['@finance/pay/request'],
                'as commentBehavior' => [
                    'class' => \hipanel\modules\finance\behaviors\PayButtonCommentBehavior::class,
                ],
            ],
            \hipanel\modules\finance\logic\ServerTariffCalculatorInterface::class => \hipanel\modules\finance\logic\CalculatorInterface::class,
            \hipanel\modules\finance\logic\CalculatorInterface::class => \hipanel\modules\finance\logic\Calculator::class,
        ],
        'singletons' => [
            hipanel\modules\finance\providers\BillTypesProvider::class => hipanel\modules\finance\providers\BillTypesProvider::class,
            hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface::class => hipanel\modules\finance\transaction\ApiTransactionRepository::class,
            hipanel\modules\finance\logic\bill\BillQuantityFactoryInterface::class => hipanel\modules\finance\logic\bill\BillQuantityFactory::class,
            hipanel\modules\finance\models\ServerResourceTypesProviderInterface::class => hipanel\modules\finance\models\ServerResourceTypesProvider::class,
            hipanel\modules\finance\cart\storage\CartStorageInterface::class => function (yii\di\Container $container) {
                return hipanel\modules\finance\cart\storage\CartStorageFactory::forUser($container->get(\yii\web\User::class));
            },
            \hipanel\modules\finance\models\factories\PriceModelFactory::class => \hipanel\modules\finance\models\factories\PriceModelFactory::class,
            \hipanel\modules\finance\grid\presenters\price\PricePresenterFactory::class => \hipanel\modules\finance\grid\presenters\price\PricePresenterFactory::class,
            \hipanel\modules\finance\widgets\FormulaHelpModal::class => \hipanel\modules\finance\widgets\FormulaHelpModal::class,
        ],
    ],
];
