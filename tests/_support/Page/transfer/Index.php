<?php

namespace hipanel\modules\finance\tests\_support\Page\transfer;

use hipanel\tests\_support\Page\IndexPage;

class Index extends IndexPage
{
    public function seeTransferActionSuccess(): void
    {
        $I = $this->tester;

        $I->closeNotification('Transfer was completed');
        $I->seeInCurrentUrl('finance/bill/index');
    }
}
