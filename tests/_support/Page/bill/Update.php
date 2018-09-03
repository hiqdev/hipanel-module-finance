<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\tests\_support\AcceptanceTester;
use hipanel\tests\_support\Page\Widget\Select2;

class Update extends Create
{
    protected $select2;

    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I);

        $this->select2 = new Select2($I);
    }

    /**
     * @param string $billId - id of bill that should be updated.
     */
    public function goToUpdatePage(string $billId): void
    {
        $I = $this->tester;

        $I->click("//tr[@data-key=$billId]/td/div/button");
        $I->click("a[href='/finance/bill/update?id=$billId']");

        $I->seeInCurrentUrl('finance/bill/update?id=' . $billId);
    }

    /**
     *  Checks whether a bill was updated successfully.
     */
    public function seeBillWasUpdated(): void
    {
        $I = $this->tester;

        $I->closeNotification('Bill was updated successfully');
        $I->seeInCurrentUrl('/finance/bill?id');
    }
}
