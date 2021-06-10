<?php

namespace hipanel\modules\finance\tests\_support\Page\transfer;

use hipanel\tests\_support\Page\Authenticated;

class Index extends Authenticated
{
    public function seeTransferActionSuccess(): void
    {
        $I = $this->tester;

        $I->closeNotification('Transfer was completed');
        $I->seeInCurrentUrl('finance/bill/index');
    }
}
