<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\helpers\Url;

class Copy extends Authenticated
{
    public function copyBill($billId): void
    {
        $I = $this->tester;
        
        $I->needPage(Url::to('@bill/copy?id=' . $billId));
        $I->wait(5);
        $I->pressButton('Save');
    }
}
