<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

use hipanel\modules\finance\grid\TargetGridView;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetResource;
use hipanel\modules\finance\models\TargetSearch;

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
        '@tariffprofile' => '/finance/tariff-profile',
        '@requisite' => '/finance/requisite',
        '@target' => '/finance/target',
        '@target-resource' => '/finance/targetresource',
    ],
    'modules' => [
        'finance' => [
            'class' => \hipanel\modules\finance\Module::class,
        ],
        'cart' => array_filter([
            'class' => \hiqdev\yii2\cart\Module::class,
            'termsPage' => $params['organization.termsUrl'] ?: $params['organization.url'],
            'orderPage' => '/finance/cart/select',
            'paymentMethodsProvider' => \yii\di\Instance::of(\hipanel\modules\finance\providers\PaymentMethodsProvider::class),
            'shoppingCartOptions' => [
                'on cartChange' => [\hipanel\modules\finance\cart\CartCalculator::class, 'handle'],
                'session' => \hipanel\modules\finance\cart\storage\CartStorageInterface::class,
            ],
        ]),
        'merchant' => [
            'class' => \hiqdev\yii2\merchant\Module::class,
            'returnPage' => '/finance/pay/return',
            'notifyPage' => '/finance/pay/notify',
            'finishPage' => '/finance/bill',
            'purchaseRequestCollectionClass' => \hipanel\modules\finance\merchant\PurchaseRequestCollection::class,
            'currenciesCollectionClass' => \hipanel\modules\finance\merchant\CurrenciesCollection::class,
            'cashewOnly' => $params['module.finance.merchant.pay.cashew.only'] ?? false,
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
                [
                    'pattern' => 'finance/pay/return/<username:.+>/<merchant:[\w\d_]+>/<transactionId:[\w\d]+>',
                    'route' => 'finance/pay/return',
                ],
            ],
        ],
        'themeManager' => [
            'pathMap' => [
                dirname(__DIR__) . '/src/views' => '$themedViewPaths',
                dirname(__DIR__) . '/src/widgets/views' => '$themedWidgetPaths',
            ],
        ],
        'i18n' => [
            'translations' => [
                'hipanel:finance' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel:finance:change' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel:finance:tariff' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel.finance.units' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel:finance:tariff:types' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                    'forceTranslation' => true,
                ],
                'hipanel:finance:deposit' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel:finance:sale' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel.finance.suggestionTypes' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel.finance.price' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel.finance.billTypes' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel.finance.plan' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
                ],
                'hipanel.finance.tariffprofile' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => dirname(__DIR__) . '/src/messages',
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
            hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface::class => hipanel\modules\finance\logic\bill\QuantityFormatterFactory::class,
            hipanel\modules\finance\models\ServerResourceTypesProviderInterface::class => hipanel\modules\finance\models\ServerResourceTypesProvider::class,
            hipanel\modules\finance\cart\storage\CartStorageInterface::class => function (yii\di\Container $container) {
                return hipanel\modules\finance\cart\storage\CartStorageFactory::forUser($container->get(\yii\web\User::class));
            },
            \hipanel\modules\finance\models\factories\PriceModelFactory::class => \hipanel\modules\finance\models\factories\PriceModelFactory::class,
            \hipanel\modules\finance\grid\presenters\price\PricePresenterFactory::class => \hipanel\modules\finance\grid\presenters\price\PricePresenterFactory::class,
            \hipanel\modules\finance\widgets\FormulaHelpModal::class => \hipanel\modules\finance\widgets\FormulaHelpModal::class,

            \Money\Currencies::class => function (\yii\di\Container $container) {
                return new \Money\Currencies\AggregateCurrencies([
                    new \Money\Currencies\ISOCurrencies(),
                ]);
            },
            \Money\MoneyFormatter::class => function (\yii\di\Container $container) {
                return new \Money\Formatter\IntlMoneyFormatter(
                    new NumberFormatter(Yii::$app->language, \NumberFormatter::CURRENCY),
                    $container->get(Money\Currencies::class)
                );
            },
            \Money\MoneyParser::class => \Money\Parser\DecimalMoneyParser::class,
            'target-resource-config' => static fn() => ResourceConfigurator::build()
                ->setModelClassName(Target::class)
                ->setSearchModelClassName(TargetSearch::class)
                ->setToObjectUrl('@target/resource-detail')
                ->setGridClassName(TargetGridView::class)
                ->setResourceModelClassName(TargetResource::class)
                ->setSearchView('@vendor/hiqdev/hipanel-module-finance/src/views/target/_search')
                // TODO: Unhardcode resource types
                ->setColumns(['server_sata', 'server_ssd', 'server_du', 'cdn_traf', 'cdn_traf_max', 'server_traf', 'server_traf_in', 'server_traf95', 'server_traf95_in']),
        ],
    ],
];
