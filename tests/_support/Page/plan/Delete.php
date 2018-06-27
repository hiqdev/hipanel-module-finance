<?php

namespace hipanel\modules\finance\tests\_support\Page\plan;

class Delete extends View
{
    public function deletePlan()
    {
        $I = $this->tester;

        $this->visitPlan();
        $I->click("//a[@href='/finance/plan/delete?id={$this->id}']");
        $I->acceptPopup();
        $I->closeNotification('Plan was successfully deleted');
    }
}
