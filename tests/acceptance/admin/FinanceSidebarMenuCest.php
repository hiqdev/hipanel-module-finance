<?php

namespace hipanel\modules\finance\tests\acceptance\admin;

use hipanel\tests\_support\Page\SidebarMenu;
use hipanel\tests\_support\Step\Acceptance\Admin;

class FinanceSidebarMenuCest
{
    public function ensureMenuIsOk(Admin $I)
    {
        (new SidebarMenu($I))->ensureDoesNotContain('Finance');
    }
}
