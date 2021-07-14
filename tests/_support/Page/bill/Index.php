<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\IndexPage;

class Index extends IndexPage
{   
    public function seeUpdateSuccess(): ?string
    {
        $I = $this->tester;

        $I->closeNotification('Bill was updated successfully');
        $I->seeInCurrentUrl('/finance/bill?id');

        return $this->grabBillIdFromUrl();
    }

    public function checkBillDataInBulkTable(array $bill): void
    {
        $selector = "//div[@class='dataTables_wrapper form-inline']";
        unset($bill['quantity']);
        foreach ($bill as $element) {
            $this->tester->see($element, $selector);
        }
    }
}
