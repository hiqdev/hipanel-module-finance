<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\Authenticated;

class Index extends hipanel\tests\_support\Page\IndexPage
{   
    public function seeUpdateSuccess(): ?string
    {
        $I = $this->tester;

        $I->closeNotification('Bill was updated successfully');
        $I->seeInCurrentUrl('/finance/bill?id');

        $id = $this->grabBillIdFromUrl();
        return $id;
    }
}
