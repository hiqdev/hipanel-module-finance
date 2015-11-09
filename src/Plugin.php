<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (https://hiqdev.com/)
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
            ],
            'menus' => [
                'hipanel\modules\finance\SidebarMenu',
            ],
            'modules' => [
                'finance' => [
                    'class' => 'hipanel\modules\finance\Module',
                ],
                'merchant' => [
                    'class' => 'hiqdev\yii2\merchant\Module',
                    'defaults' => [
                        'confirmPage' => '/finance/pay/confirm',
                    ],
                    'merchants' => function ($params) {
                        $params = array_merge([
                            'site'     => Yii::$app->request->getHostInfo(),
                            'username' => Yii::$app->user->identity->username,
                        ], (array)$params);
                        $ms = Merchant::findAll($params, ['scenario' => 'prepare-info']);
                        foreach ($ms as $m) {
                            if ($m->system == 'wmdirect') {
                                continue;
                            }
                            $merchants[$m->name] = $m->getAttributes();
                        }

                        return $merchants;
                    },
                ],
            ],
        ];
    }
}
