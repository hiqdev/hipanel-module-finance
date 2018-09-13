<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

class Update extends Create
{
    /**
     *  Checks whether a bill was updated successfully.
     */
    public function seeActionSuccess(): string
    {
        $I = $this->tester;

        $I->closeNotification('Bill was updated successfully');
        $I->seeInCurrentUrl('/finance/bill?id');

        return "";
    }
}
