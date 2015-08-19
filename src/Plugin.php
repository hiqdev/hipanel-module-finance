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

class Plugin extends \hiqdev\pluginmanager\Plugin
{
    protected $_items = [
        'aliases' => [
            '@bill'   => '/finance/bill',
            '@tariff' => '/finance/tariff',
        ],
        'menus' => [
            [
                'class' => 'hipanel\modules\finance\SidebarMenu',
            ],
        ],
        'modules' => [
            'finance' => [
                'class' => 'hipanel\modules\finance\Module',
            ],
        ],
    ];
}
