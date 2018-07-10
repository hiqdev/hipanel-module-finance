<?php

namespace hipanel\modules\finance\tests\acceptance\client;

use hipanel\tests\_support\Page\SidebarMenu;
use hipanel\tests\_support\Step\Acceptance\Client;

class FinanceSidebarMenuCest
{
    public function ensureMenuIsOk(Client $I)
    {
        $menu = new SidebarMenu($I);

        $menu->ensureContains('Finance',[
            'Payments' => '@bill/index',
            'Recharge account' => '@pay/deposit',
        ]);

        $menu->ensureDoesNotContain('Finance', [
            'Tariffs',
            'Requisites',
            'Held payments',
            'Sales',
        ]);
    }
}
