<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\TestableInput;
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

<<<<<<< HEAD
    public function setAdvancedFilter(TestableInput $filter, string $value): void
    {
        $filter->setValue($value);
=======
    public function ensureBillViewContainData(array $billData): void
    {
        foreach ($billData as $billInfo) {
            $this->tester->see($billInfo, '//table');
        }
>>>>>>> c108d27bb4652ab6d7f165cc7d952ca0646c4674
    }
}
