<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\tests\_support\Page\SidebarMenu;
use hipanel\tests\_support\Step\Acceptance\Seller;

class FinanceSidebarMenuCest
{
    public function ensureMenuIsOk(Seller $I)
    {
        (new SidebarMenu($I))->ensureContains('Finance', [
            'Payments'         => '@bill/index',
            'Recharge account' => '@pay/deposit',
            'Requisites'       => '@bill/requisites',
            'Held payments'    => '@finance/held-payments/index',
            'Sales'            => '@sale/index',
            'Tariff plans'     => '@plan/index',
            'Prices'           => '@price/index',
            'Tariff profiles'  => '@tariffprofile/index',
        ]);
    }
}
