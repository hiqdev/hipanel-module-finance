<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\acceptance\client;

use hipanel\tests\_support\Page\SidebarMenu;
use hipanel\tests\_support\Step\Acceptance\Client;

class FinanceSidebarMenuCest
{
    public function ensureMenuIsOk(Client $I)
    {
        $menu = new SidebarMenu($I);

        $menu->ensureContains('Finance', [
            'Payments' => '@bill/index',
            'Recharge account' => '@pay/deposit',
//            'Tariffs' => '@plan/index', TODO: when changed links from `tariffs` to `plans` - uncomment!
        ]);

        $menu->ensureDoesNotContain('Finance', [
            'Requisites',
            'Held payments',
            'Sales',
        ]);
    }
}
