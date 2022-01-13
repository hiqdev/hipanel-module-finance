<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\helpers\Url;

class Copy extends Authenticated
{
    public function copyBill(string $billId): void
    {
        $I = $this->tester;

        $I->needPage(Url::to('@bill/copy?id=' . $billId));
        $I->pressButton('Save');
    }

    public function seeActionSuccess(): ?string
    {
        $I = $this->tester;

        $I->closeNotification('Bill was created successfully');
        $I->seeInCurrentUrl('/finance/bill?id');

        return $I->grabFromCurrentUrl('~id_in%5B0%5D=(\d+)~');
    }
}
