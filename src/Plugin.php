<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance;

class Plugin extends \hiqdev\pluginmanager\Plugin
{
    protected $_items = [
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
