<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance;

use hipanel\modules\finance\models\Merchant;
use Yii;

class Plugin extends \hiqdev\pluginmanager\Plugin
{
    public function items()
    {
        return [
            'aliases' => [
                '@bill'   => '/finance/bill',
                '@purse'  => '/finance/purse',
                '@tariff' => '/finance/tariff',
                '@pay'    => '/merchant/pay',
                '@cart'   => '/cart/cart',
            ],
            'menus' => [
                'hipanel\modules\finance\SidebarMenu',
            ],
            'modules' => [
                'finance' => [
                    'class' => 'hipanel\modules\finance\Module',
                ],
                'cart' => [
                    'class'          => 'hiqdev\yii2\cart\Module',
                    'termsPage'      => Yii::$app->params['orgUrl'] . 'rules',
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
                ],
                'merchant' => [
                    'class'           => 'hiqdev\yii2\merchant\Module',
                    'notifyPage'      => '/finance/pay/notify',
                    'depositClass'    => 'hipanel\modules\finance\merchant\Deposit',
                    'collectionClass' => 'hipanel\modules\finance\merchant\Collection',
                ],
            ],
        ];
    }
}
