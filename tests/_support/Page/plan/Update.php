<?php

namespace hipanel\modules\finance\tests\_support\Page\plan;

use hipanel\helpers\Url;

class Update extends Create
{
    protected function loadPage()
    {
        $I = $this->tester;

        $I->needPage(Url::to(['@plan/update', 'id' => $this->id]));
    }

    protected function savePlan()
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification("Plan was successfully updated");
    }

    public function updatePlan($id)
    {
        $this->id = $id;

        return $this->createPlan();
    }
}
