<?php

namespace hipanel\modules\finance\tests\_support\Page\transfer;

use hipanel\tests\_support\Page\IndexPage;

class Index extends IndexPage
{
    public function seeTransferActionSuccess(): void
    {
        $this->tester->closeNotification('Transfer was completed', 'Not enought balance');
        $this->tester->seeInCurrentUrl('finance/bill/index');
    }
}
