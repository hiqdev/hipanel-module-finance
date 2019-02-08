<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Manager;

class BillCest
{
    public $billId;

    public function ensureBillPageWorks(Manager $I): void
    {
        $I->login();
        $I->needPage(Url::to('@bill'));
    }
}
